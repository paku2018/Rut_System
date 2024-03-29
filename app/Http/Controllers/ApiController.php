<?php

namespace App\Http\Controllers;

use App\Common\ApiResponseData;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\SubOrder;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Mockery\Exception;

class ApiController extends Controller
{
    //
    //login
    public function login(Request $request)
    {
        $input = $request->all();
        $messages = [
        ];
        $customAttributes = [
            'email' => 'Email',
            'password' => 'Contraseña',
        ];
        $validator = Validator::make($input,
            [
                'email' => ['required', 'exists:users,email'],
                'password' => ['required', 'string'],
            ], $messages, $customAttributes);

        $responseData = new ApiResponseData($request);

        try {
            if ($validator->fails()) {
                $responseData->status = self::ERROR;
                $errors = $validator->errors();
                if ($errors->has('email')) {
                    $responseData->message =  self::ERR_INVALID_USER_EMAIL;
                }
                else {
                    $responseData->message =  self::ERR_INVALID_PASSWORD;
                }
                return response()->json($responseData);
            }
        }
        catch (Exception $e){
            Log::info('$e : ' . $e->getMessage());
        }
        // get user object
        if(Auth::attempt(['email' => $input['email'], 'password' => $input['password'], 'role' => 'waiter'])){
            $user = Auth::user();
            $success = array(
                'token' =>  $user->createToken(config('app.name'))-> accessToken,
                'user' => $user
            );
            $responseData->result = $success;
            $responseData->message = "success";
            $responseData->status = self::SUCCESS;
            return response()->json($responseData);
        }
        else{
            $responseData->status = self::ERROR;
            $responseData->message = self::ERR_INVALID_USER;
            return response()->json($responseData);
        }
    }

    public function index(Request $request){
        $resId = Auth::user()->restaurant_id;
        $responseData = new ApiResponseData($request);

        $tables = Table::where('restaurant_id', $resId)->where('type', 'real')->orderBy('type', 'ASC')->orderBy('t_number', 'ASC')->get();
        $products = Product::where('restaurant_id', $resId)->where('status',1)->get();
        $agg_products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
            ->whereHas('category', function ($query) {
                $query->where('restaurant_id', 0);
            })->get();
        $success = array(
            'tables' =>  $tables,
            'products' => $products,
            'agg_products' => $agg_products
        );
        $responseData->result = $success;
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }

    public function getTableInfo(Request $request){
        $tableId = $request->tableId;
        $responseData = new ApiResponseData($request);
        $table = Table::with('restaurant')->find($tableId);
        $orders = Order::with('client','product', 'children')->where('status','open')->where('assigned_table_id', $tableId)->get();
        $success = array(
            'table' =>  $table,
            'orders' => $orders
        );
        $responseData->result = $success;
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }

    public function deleteOrder(Request $request){
        $tableId = $request->tableId;
        $orders = $request->orders;
        $orders = explode(",", $orders);
        $responseData = new ApiResponseData($request);
        $delete = Order::whereIn('id', $orders)->delete();

        //check if pending order and if not, change the table status
        $pend_order = Order::where('assigned_table_id', $tableId)->where('status', '!=', 'done')->where('deliver_status', 0)->first();
        if (!$pend_order)
            $update = Table::where('id', $tableId)->update(['status'=>'open']);

        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }
    public function createAndAssign(Request $request){
        try{
            $tableId = $request->tableId;
            $table = Table::find($tableId);
            $items = $request->items;
            $items = json_decode($items);
            foreach ($items as $key => $item){
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
                        if ($index) {
                            $subData = [
                                'order_id' => $order->id,
                                'product_id' => $index
                            ];
                            SubOrder::create($subData);
                        }
                    }
                }
            }
            $update = Table::where('id', $tableId)->update(['status'=>'ordered']);
            $result = true;
        }catch (\Exception $e){
            Log::info('order create error:'.$e->getMessage());
            $result = false;
        }
        $responseData = new ApiResponseData($request);
        $success = array(
            'result' =>  $result,
        );
        $responseData->result = $success;
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }
    public function saveComment(Request $request){
        $responseData = new ApiResponseData($request);
        $tableId = $request->tableId;
        $table = Table::with('restaurant')->find($tableId);
        $order = Order::where('restaurant_id', $table->restaurant_id)->where('client_id', $table->current_client_id)->where('assigned_table_id', $tableId)
            ->where('status', 'open')->whereNull('comment')->first();
        if(isset($order)){
            Order::where('id', $order->id)->update(['comment' => $request->comment]);
        }
        else{
            $order = Order::where('restaurant_id', $table->restaurant_id)->where('client_id', $table->current_client_id)->where('assigned_table_id', $tableId)
                ->where('status', 'open')->orderBy('updated_at', 'asc')->first();
            if(isset($order)){
                Order::where('id', $order->id)->update(['comment' => $request->comment]);
            }
            else{
                $responseData->message = self::ERROR;
                $responseData->status = self::ERR_INVALID_UNKNOWN;
                return response()->json($responseData);
            }
        }
        $orders = Order::with('client', 'product')->where('status', 'open')->where('assigned_table_id', $tableId)->get();
        $success = array(
            'table' => $table,
            'orders' => $orders
        );

        $responseData->result = $success;
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }
    public function pend(Request $request){
        $tableId = $request->tableId;
        $update = Table::where('id', $tableId)->update(['status'=>'pend']);

        $responseData = new ApiResponseData($request);
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }
    public function deliver(Request $request){
        $tableId = $request->tableId;
        $orders = $request->orders;
        $orders = explode(",", $orders);
        $update = Order::whereIn('id', $orders)->update(['deliver_status'=>1]);
        $delivered_orders = Order::whereIn('id', $orders)->where('deliver_status', 0)->get();

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
        if (!$pend_order)
            $update = Table::where('id', $tableId)->update(['status'=>'open']);

        $responseData = new ApiResponseData($request);
        $responseData->message = "success";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }

    public function logout(Request $request){
        $token = $request->user()->token();
        $token->revoke();
        $responseData = new ApiResponseData($request);
        $responseData->message = "logout";
        $responseData->status = self::SUCCESS;
        return response()->json($responseData);
    }
}
