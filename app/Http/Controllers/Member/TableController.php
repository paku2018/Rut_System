<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class TableController extends Controller
{
    public function index(){
        $resId = Auth::user()->restaurant_id;
        $tables = Table::where('restaurant_id', $resId)->where(function ($q){
            $q->where('type', 'real')
                ->orWhere(function ($query){
                   $query->where('type', '!=', 'real')
                       ->where('status', '!=', 'closed');
                });
        })->orderBy('type', 'ASC')->orderBy('t_number', 'ASC')->get();
        $products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
            ->whereHas('category', function ($query) {
                $query->where('restaurant_id', '!=', 0);
            })->get();
        $agg_products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
            ->whereHas('category', function ($query) {
                $query->where('restaurant_id', 0);
            })->get();

        return view('member.table.index',compact('tables','products', 'agg_products'));
    }

    public function getList(Request $request) {
        $resId = Auth::user()->restaurant_id;
        if ($resId) {
//            $tables = Table::where('restaurant_id', $resId)->where(function ($q){
//                $q->where('type', 'real')
//                    ->orWhere(function ($query){
//                        $query->where('type', '!=', 'real')
//                            ->where('status', '!=', 'closed');
//                    });
//            })->orderBy('type', 'ASC')->orderBy('t_number', 'ASC')->get();
            $tables = Table::where('restaurant_id', $resId)->where('type', 'real')->orderBy('type', 'ASC')->orderBy('t_number', 'ASC')->get();
            return response()->json(['result'=>true, 'data'=>$tables]);
        }else {
            return response()->json(['result'=>true, 'data'=>array()]);
        }
    }

    public function getTableInfo(Request $request){
        $tableId = $request->tableId;
        $table = Table::with('restaurant')->find($tableId);
        $orders = Order::with('client','product', 'children')->where('status','open')->where('assigned_table_id', $tableId)->get();

        return response()->json(['result'=>true, 'data'=>$table, 'orders'=> $orders]);
    }

    public function pend(Request $request){
        $tableId = $request->tableId;
        $update = Table::where('id', $tableId)->update(['status'=>'pend']);

        return response()->json(['status'=>true, 'result'=>true]);
    }


    //function for cashiers
    public function cashierIndex(){
        $resId = Auth::user()->restaurant_id;
        $tables = Table::where('restaurant_id', $resId)->get();
        return view('member.table.index_cashier',compact('tables'));
    }

    public function deliver(Request $request){
        $result = false;
        try {
            $tableId = $request->tableId;
            $orders = $request->orders;
            $orders = explode(",", $orders);
            $delivered_orders = Order::whereIn('id', $orders)->where('deliver_status', 0)->get();

            //update deliver status
            $update = Order::whereIn('id', $orders)->update(['deliver_status'=>1]);

            //send confirmation email to the client
            if (count($delivered_orders) > 0 && $delivered_orders[0]->client_id) {
                $client = Client::find($delivered_orders[0]->client_id);
                $data = array();
                $total = 0;
                foreach ($delivered_orders as $one) {
                    $item = array();
                    $product = Product::find($one->product_id);
                    $item['product_name'] = $product->name;
                    $item['product_price'] = $product->sale_price;
                    $item['product_count'] = $one->order_count;
                    $total += $product->sale_price * $one->order_count;

                    $data['products'][] = $item;
                }
                $data['total'] = $total;

                sendOrderDeliverEmail($data, $client->email);
            }

            //check if pending order and if not, change the table status
            $pend_order = Order::where('assigned_table_id', $tableId)->where('status', '!=', 'done')->where('deliver_status', 0)->first();
            if (!$pend_order) {
                $update = Table::where('id', $tableId)->update(['status'=>'open']);
            }

            $result = true;
        }catch (\Exception $e) {
            Log::info("delivery error : ".$e->getMessage());
        }

        return response()->json(['status'=>true, 'result'=>$result]);
    }

    public function deleteOrder(Request $request){
        $tableId = $request->tableId;
        $orders = $request->orders;
        $orders = explode(",", $orders);

        $delete = Order::whereIn('id', $orders)->delete();

        //check if pending order and if not, change the table status
        $pend_order = Order::where('assigned_table_id', $tableId)->where('status', '!=', 'done')->where('deliver_status', 0)->first();
        if (!$pend_order)
            $update = Table::where('id', $tableId)->update(['status'=>'open']);

        return response()->json(['status'=>true, 'result'=>true]);
    }
}
