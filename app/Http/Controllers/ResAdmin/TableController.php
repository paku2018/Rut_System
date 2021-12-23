<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class TableController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        if ($resId){
            $tables = Table::where('restaurant_id', $resId)->where(function ($q){
                $q->where('type', 'real')
                    ->orWhere(function ($query){
                        $query->where('type', '!=', 'real')
                            ->where('status', '!=', 'closed');
                    });
            })->orderBy('type', 'asc')->get();
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.table.index', compact('tables','restaurant'));
        }else{
            abort(404);
        }
    }

    public function create(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.table.edit', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function edit($id){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $data = Table::find($id);

            return view('resAdmin.table.edit', compact('restaurant', 'data'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request){
        try{
            $table_id = $request->id;
            $data = $request->only('t_number','name');
            $data['restaurant_id'] = session()->get('resId');
            $table = Table::updateOrCreate(['id'=>$table_id], $data);

            return Redirect::route('restaurant.tables.list');
        }catch (\Exception $e){
            session()->flash('server_error',true);
            return Redirect::back();
        }
    }

    public function delete(Request $request){
        $id = $request->id;
        try{
            $table = Table::find($id);
            $result = $table->delete();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("table delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }
    }

    public function close(Request $request){
        $tableId = $request->tableId;
        $table = Table::find($tableId);
        if (!$table){
            abort(404);
        }
        try{
            //record payment
            $data = $request->only('consumption', 'tip', 'shipping', 'payment_method', 'document_type');
            $data['table_id'] = $tableId;
            $data['client_id'] = $table->current_client_id;
            $data['restaurant_id'] = $table->restaurant_id;

            $payment = Payment::create($data);

            //update order and table
            $order_update = Order::where('assigned_table_id', $tableId)->where('status','open')->update(['status'=>'done', 'final_payment_id'=>$payment->id]);
            $update = Table::where('id', $tableId)->update(['status'=>'closed', 'current_client_id'=>null]);
        }catch (\Exception $e){
            session()->flash('payment_error',true);
        }

        return Redirect::back();
    }
}
