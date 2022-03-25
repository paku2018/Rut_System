<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\SubOrder;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class OrderController extends Controller
{
    public function getData(){
        $resId = Auth::user()->restaurant_id;
        $orders = Order::with('client', 'product')->where('restaurant_id', $resId)->where('status','open')->whereNull('assigned_table_id')->orderBy('user_id','asc')->get();

        return response()->json(['result'=>true, 'data'=>$orders]);
    }

    public function assign(Request $request){
        $orders = $request->orders;
        $orders = explode(",", $orders);
        $tableId = $request->tableId;
        try{
            $update = Order::whereIn('id', $orders)->update(['assigned_table_id'=>$tableId]);
            $table_update = Table::where('id', $tableId)->update(['status'=>'open']);
            $result = true;
        }catch (\Exception $e){
            Log::info("assign order error:".$e->getMessage());
            $result = false;
        }

        return response()->json(['status'=>true, 'result'=>$result]);
    }

    public function createAndAssign(Request $request){
        try{
            $tableId = $request->tableId;
            $table = Table::find($tableId);
            $items = $request->items;
            $items = json_decode($items);
            foreach ($items as $item){
                if($item){
                    $data = [
                        'restaurant_id' => $table->restaurant_id,
                        'product_id' => $item->id,
                        'order_count' => $item->quantity,
                        'status' => 'open',
                        'client_id' => $table->current_client_id,
                        'comment' => $request->comment,
                        'assigned_table_id' => $tableId
                    ];
                    $order = Order::create($data);

                    //adding sub orders
                    $indexes = $item->sub_orders;
                    $indexes = explode(",", $indexes);
                    foreach ($indexes as $index) {
                        $subData = [
                            'order_id' => $order->id,
                            'product_id' => $index
                        ];
                        SubOrder::create($subData);
                    }
                }
            }
            $update = Table::where('id', $tableId)->update(['status'=>'ordered']);
            $result = true;
        }catch (\Exception $e){
            Log::info('order create error:'.$e->getMessage());
            $result = false;
        }

        return response()->json(['status'=>true, 'result'=>$result]);
    }
}
