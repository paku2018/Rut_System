<?php

namespace App\Http\Controllers\Member;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Product;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        })->orderBy('type', 'asc')->orderBy('t_number', 'ASC')->get();
        $products = Product::where('restaurant_id', $resId)->where('status',1)->get();
        return view('member.table.index',compact('tables','products'));
    }

    public function getTableInfo(Request $request){
        $tableId = $request->tableId;
        $table = Table::with('restaurant')->find($tableId);
        $orders = Order::with('client','product')->where('status','open')->where('assigned_table_id', $tableId)->get();

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
        $tableId = $request->tableId;
        $orders = $request->orders;
        $orders = explode(",", $orders);
        $update = Order::whereIn('id', $orders)->update(['deliver_status'=>1]);

        //check if pending order and if not, change the table status
        $pend_order = Order::where('assigned_table_id', $tableId)->where('status', '!=', 'done')->where('deliver_status', 0)->first();
        if (!$pend_order)
            $update = Table::where('id', $tableId)->update(['status'=>'open']);

        return response()->json(['status'=>true, 'result'=>true]);
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
