<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {

    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        $user = Auth::user();
        if ($user->email_verified_at == null){
            return redirect()->route('verify');
        }
        $role = $user->role;

        switch ($role){
            case 'admin':
            case 'restaurant':
                $link = $role.".home";
                break;
            case 'waiter':
            case 'cashier':
                $link = $role.".tables";
                break;
            default:
                $link = '';
                break;
        }
        if($role == "client"){
            $resCode = session()->get('resCode');
            if ($resCode)
                return redirect()->route('restaurant-menu',$resCode);
            else
                abort(404);
        }else{
            return redirect()->route($link);
        }
    }

    public function profile(){
        $data = Auth::user();

        return view('profile', compact('data'));
    }

    public function updateProfile(Request $request){
        try{
            $user_id = Auth::id();
            $data = $request->only('name');
            if(isset($request->change_password)){
                $data['password'] = Hash::make($request->new_password);
            }

            User::updateOrCreate(['id'=>$user_id], $data);
            session()->flash('update_success',true);
        }catch (\Exception $e){
            session()->flash('server_error',true);
        }
        return Redirect::back();
    }

    public function menu($code){
        try{
            session()->put('resCode', $code);
            $resId = substr($code, 7, strlen($code)-10);
            $restaurant = Restaurant::find($resId);
            if ($restaurant){
                $products = Product::where('restaurant_id', $resId)->where('status', 1)->orderBy('created_at', 'desc')->get();
                $tables = Table::where('restaurant_id', $resId)->where('status', '!=', 'pend')->get();

                return view('menu', compact('restaurant','products','tables'));
            }else{
                abort(404);
            }
        }catch (\Exception $e){
            abort(404);
        }
    }

    public function verify(){
        $user = Auth::user();
        if($user)
            return view('auth.verify',compact('user'));
        else
            abort(404);
    }

    public function checkCode(Request $request){
        $user = Auth::user();
        if($user)
        {
            if($request->code==$user->verification_code){
                $user->email_verified_at = date('Y-m-d H:i:s');
                $user->save();
                return response()->json(['result'=>true,'success'=>true]);
            }
            else
                return response()->json(['result'=>true,'success'=>false]);
        }
        else
            return response()->json(['result'=>false]);
    }

    public function resendCode(Request $request){
        $user = Auth::user();
        if($user)
        {
            try{
                $random = rand(100000, 999999);
                sendVerifyEmail($random,$user->email);
                $user->verification_code = $random;
                $user->save();
                return response()->json(['result'=>true,'success'=>false]);
            }catch (\Exception $e){
                return response()->json(['result'=>false]);
            }
        }
        else
            return response()->json(['result'=>false]);
    }

    public function order(Request $request){
        try{
            $email = $request->email;
            $code = $request->code;
            $orderType = $request->orderType;
            $tableId = $request->tableId;

            //check verification code
            $client = Client::where('email', $email)->where('v_code', $code)->first();
            if(!$client){
                return response()->json(['result'=>true, 'success'=>false, 'error'=>'code_error']);
            }

            $resCode = session()->get('resCode');
            $resId = substr($resCode, 7, strlen($resCode)-10);
            //check table is available
            if($orderType == 0){
                $table = Table::find($tableId);
                if($table->status == "pend"){
                    return response()->json(['result'=>true, 'success'=>false, 'error'=>'table_disable']);
                }else if($table->status == "open" && $table->current_client_id != $client->id){
                    return response()->json(['result'=>true, 'success'=>false, 'error'=>'table_disable']);
                }
            }else{
                //create virtual table
                $table_data = [
                    'restaurant_id' => $resId,
                    't_number' => $client->id,
                    'name' => 'Virtual-'.$client->id."-".date('His'),
                    'current_client_id' => $client->id,
                    'type' => 'delivery',
                    'status' => 'open'
                ];
                $table = Table::create($table_data);
            }
            $items = $request->items;
            $items = json_decode($items);

            foreach ($items as $key => $item){
                if($item){
                    $data = [
                        'restaurant_id' => $resId,
                        'product_id' => $key,
                        'order_count' => $item,
                        'client_id' => $client->id,
                        'assigned_table_id' => $table->id,
                        'status' => 'open'
                    ];
                    Order::create($data);
                }
            }
            if ($orderType == 0){
                $update_table = Table::where('id', $tableId)->update(['status'=>'open', 'current_client_id'=>$client->id]);
            }

            //remove client v_code
            $client->v_code = null;
            $client->save();
            return response()->json(['result'=>true, 'success'=>true]);
        }catch (\Exception $e){
            return response()->json(['result'=>true, 'success'=>false]);
        }
    }

    public function sendVerificationMail(Request $request){
        $email = $request->email;
        try{
            $code = rand(100000, 999999);
            sendOrderVerifyEmail($code, $email);

            $client = Client::updateOrCreate(['email'=>$email], ['email'=>$email, 'v_code'=>$code]);

            $result = true;
        }catch (\Exception $e){
            Log::info("Order Verify Email error:".$e->getMessage());
            $result = false;
        }

        return response()->json(['result'=>true, 'success'=>$result]);
    }
}
