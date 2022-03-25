<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Client;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\SubOrder;
use App\Models\Table;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

use App\CustomClass\ApiTaco;
use App\CustomClass\CreateReceipt;
use App\CustomClass\FinalReceipt;

class TableController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        //dd(env('TACO_API_URL_PROD'));
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
                ->whereHas('category', function ($query) {
                    $query->where('restaurant_id', '!=', 0);
                })->get();
            $agg_products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
                ->whereHas('category', function ($query) {
                    $query->where('restaurant_id', 0);
                })->get();

            return view('resAdmin.table.index', compact('restaurant', 'products', 'agg_products'));
        }else{
            abort(404);
        }
    }

    public function getList(Request $request) {
        $resId = session()->get('resId');
        if ($resId) {
            $tables = Table::where('restaurant_id', $resId)->where(function ($q){
                $q->where('type', 'real')
                    ->orWhere(function ($query){
                        $query->where('type', '!=', 'real')
                            ->where('status', '!=', 'closed');
                    });
            })->orderBy('type', 'ASC')->orderBy('t_number', 'ASC')->get();
            return response()->json(['result'=>true, 'data'=>$tables]);
        }else {
            return response()->json(['result'=>true, 'data'=>array()]);
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
        $table = Table::with('restaurant')->find($tableId);
        if (!$table){
            return response()->json([
                'data'=> 'error',
                'success'=> false
            ]);
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

            $payment = Payment::with(['restaurant','table','items.client','items.product',])->find($payment->id);
            $payment->update(['history_data'=> json_encode($payment)]);
            $output_pdf_to_png = '';
            if ((int) $data['document_type']==Payment::ELECTRONIC_BALLOT) {
                //enviar a taco si es boleta
                $user_taco_id = auth()->user()->taco_user_id;
                if(!empty($user_taco_id) && $user_taco_id!='0|0'){
                    $ApiTaco = new ApiTaco($user_taco_id);
                    $ApiTaco->prepareData($payment, auth()->user());
                    $dataTaco = $ApiTaco->EmitirBoleta();
                    $payment->update(['taco_data'=> json_encode($dataTaco)]);

                    //start
                    //download pdf "libredte" and convert to image
                    if($table->restaurant->is_receipt_sii){
                        if(isset($dataTaco['response'])){
                            if(isset($dataTaco['response']['folio'])){
                                if(!empty($dataTaco['response']['folio'])){
                                    $params = [
                                        $dataTaco['response']['dte'],
                                        $dataTaco['response']['folio'],
                                        1,
                                        $dataTaco['response']['emisor'],
                                        $dataTaco['response']['fecha'],
                                        $dataTaco['response']['total'],
                                    ];
                                    $dataTaco['response']['url_pdf'] = $url_pdf = 'https://sii.pagocash.cl/dte/dte_emitidos/pdf/'.implode('/',$params).'?filename=boleta.pdf';

                                    $output_pdf = storage_path('app/public/receipts/print_'.$payment->id.'.pdf');
                                    $output_pdf_to_png = storage_path('app/public/receipts/print_'.$payment->id.'');
                                    shell_exec("curl $url_pdf --output $output_pdf");
                                    shell_exec("pdftoppm -png $output_pdf $output_pdf_to_png");
                                }
                            }
                        }
                    }
                    //end downlaoad pdf
                }
            }

            //crear ticket para imprimir
            $payment = Payment::with(['restaurant','table','items.client','items.product',])->find($payment->id);
            $result_ticket = new FinalReceipt($payment, auth()->user());
            $ticket_png = 'storage/receipts/'.$result_ticket->filename;
            if(!empty($output_pdf_to_png)){
                $url_png = 'storage/receipts/print_'.$payment->id.'-1.png';
                if(!file_exists($output_pdf_to_png.'-1.png')){
                    $url_png = !empty($result_ticket->filename) ? $ticket_png : '';
                }
            }else{
                $url_png = !empty($result_ticket->filename) ? $ticket_png : '';
            }
            sleep(2);

            return response()->json([
                ///'TACO'=> env('TACO_API_URL_PROD','_EMPTY_'),
                'url_png'=> $url_png,
                'success'=> true
            ]);

        }catch (\Exception $e){
            ///session()->flash('payment_error',true);
            \Log::debug($e);
            return response()->json([
                //'data'=> $e,
                'success'=> false
            ]);
        }

        return Redirect::back();
    }

    public function createDelivery(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
                ->whereHas('category', function ($query) {
                    $query->where('restaurant_id', '!=', 0);
                })->get();
            $agg_products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
                ->whereHas('category', function ($query) {
                    $query->where('restaurant_id', 0);
                })->get();

            return view('resAdmin.table.create-delivery', compact('restaurant', 'products', 'agg_products'));
        }else{
            abort(404);
        }
    }

    public function storeDelivery(Request $request){
        $resId = session()->get('resId');
        $table = null;
        if ($resId){
            try{
                $client_email = $request->email;
                $client_name = $request->name;
                $client_address = $request->address;
                $client = Client::where('email', $client_email);
                if($client_name) {
                    $client = $client->where('name', $client_name);
                }
                $client = $client->first();
                if(!$client){
                    $client = Client::create(['email'=>$client_email, 'name'=>$client_name, 'address'=>$client_address]);
                }

                $name = $client_name ? $client_name : $client_email;
                $table_data = [
                    'restaurant_id' => $resId,
                    't_number' => $client->id,
                    'name' => 'Virtual('.$name.")-".date('His'),
                    'current_client_id' => $client->id,
                    'type' => 'delivery',
                    'status' => 'ordered'
                ];
                $table = Table::create($table_data);

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
                            'assigned_table_id' => $table->id
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
                $update = Table::where('id', $table->id)->update(['status'=>'ordered']);
                $result = true;
            }catch (\Exception $e){
                Log::info('order create error:'.$e->getMessage());
                $result = false;
            }
        }else{
            $result = false;
        }

        return response()->json(['status'=>true, 'result'=>$result, 'data'=>$table]);
    }
}
