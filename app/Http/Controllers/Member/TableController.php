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
        $tables = Table::where('restaurant_id', $resId)->get();
        $clients = User::where('role','client')->get();
        $products = Product::where('restaurant_id', $resId)->where('status',1)->get();
        return view('member.table.index',compact('tables','clients','products'));
    }

    public function getTableInfo(Request $request){
        $tableId = $request->tableId;
        $table = Table::find($tableId);
        $orders = array();
        if ($table->status == "open"){
            $orders = Order::with('client','product')->where('status','open')->where('assigned_table_id', $tableId)->get();
        }

        return response()->json(['result'=>true, 'data'=>$table, 'orders'=> $orders]);
    }
}
