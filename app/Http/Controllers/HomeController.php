<?php

namespace App\Http\Controllers;

use App\CustomClass\ReceiptPDF;
use App\CustomClass\TestReceipt;
use App\Models\Category;
use App\Models\Client;
use App\Models\Order;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\RestaurantManger;
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
        if (!$user){
            return redirect()->route('login');
        }
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
        }elseif ($link == ''){
            auth()->logout();
            return redirect()->route('login');
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
                $categories = Category::where('restaurant_id', $resId)->orderBy('order','ASC')->get();
                $products = Product::where('restaurant_id', $resId)->where('status', 1)->orderBy('created_at', 'desc')->get();
                $tables = Table::where('restaurant_id', $resId)->where('status', '!=', 'pend')->get();

                return view('menu', compact('restaurant','products','tables', 'categories'));
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
                }else if(($table->status == "open" || $table->status == "ordered") && $table->current_client_id != $client->id){
                    return response()->json(['result'=>true, 'success'=>false, 'error'=>'table_disable']);
                }
            }else{
                //create virtual table
                $table_data = [
                    'restaurant_id' => $resId,
                    't_number' => $client->id,
                    'name' => 'Virtual('.$client->email.")-".date('His'),
                    'current_client_id' => $client->id,
                    'type' => 'delivery',
                    'status' => 'ordered'
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
                        'comment' => $request->comment,
                        'status' => 'open'
                    ];
                    Order::create($data);
                }
            }
            if ($orderType == 0){
                $update_table = Table::where('id', $tableId)->update(['status'=>'ordered', 'current_client_id'=>$client->id]);
            }

            //remove client v_code
            $client->v_code = null;
            $client->save();
            return response()->json(['result'=>true, 'success'=>true]);
        }catch (\Exception $e){
            return response()->json(['result'=>true, 'success'=>false, 'error'=>$e->getMessage()]);
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

    public function exportPdf($id){
        $table = Table::with('restaurant')->find($id);
        $restaurant = Restaurant::find($table->restaurant_id);
        $user = Auth::user();
        $rManagers = RestaurantManger::where('restaurant_id', $table->restaurant_id)->where('user_id', $user->id)->first();
        if(!$rManagers && $user->restaurant_id != $table->restaurant_id){
            abort(404);
        }

        $items = isset($_GET['items'])? $_GET['items'] : '';
        if ($items){
            $items = explode(",", $items);
            $orders = Order::with('client','product')->whereIn('id', $items)->get();
        }else{
            $orders = Order::with('client','product')->where('status','open')->where('assigned_table_id', $id)->get();
        }

        $postfix = $items = isset($_GET['items'])?str_replace(",","_",$_GET['items']):'a';
        $filename = 'receipt_'.auth()->user()->id.'_'.$id.'_'.$postfix.'.pdf';


        $myPdf = new ReceiptPDF();
        $html = $myPdf->renderHeader([
            'order_code'=> 111,
            'name'=> $restaurant->name,
            'rut'=> '11.222.333-4',
        ]);
        $html .= $myPdf->renderDetail();
        $html .= $myPdf->setItems($orders);
        $html .= $myPdf->setTotal($myPdf->total);
        //$html .= $myPdf->setPaymentDetails('EFECTIVO', 0 ,0);
        $html .= $myPdf->footer();
        $html = $myPdf->getStyles($html);

        $myPdf->pdf->AddPage();
        $myPdf->pdf->writeHTMLCell(0, 0, '', '', $html, 0, 0, 0, true, '', true);



        $export = $myPdf->pdf->Output(storage_path('app/public/'.$filename),'F');

        //$ticket = new TestReceipt(11);
        $ticket_png = 'storage/receipts/print_pre_test.png';

        return response()->json([
            'ticket_png'=> $ticket_png,
            'url_pdf'=>'storage/'.$filename,
            'success'=>true
        ]);

    }
}
