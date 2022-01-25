<?php

namespace App\Http\Controllers\ResAdmin;

use App\Export\BestProductsExportExcel;
use App\Export\SalesExportExcel;
use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use App\Services\StatisticsService;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

class StatisticsController extends Controller
{
    public function __construct(StatisticsService $statisticsService)
    {
        $this->statisticsService = $statisticsService;
    }

    public function salesIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.statistics.sales', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function getSalesData(Request $request){
        $resId = session()->get('resId');
        if ($resId){
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $result = $this->statisticsService->getSales($resId, $start_date, $end_date);

            return response()->json($result);
        }else{
            return response()->json(array());
        }
    }

    public function salesExport(Request $request){
        $resId = session()->get('resId');
        if ($resId){
            $data = array(
                'resId' => $resId,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date
            );

            $filename = __('sales').'_'.date('ymshis').'.xlsx';
            Excel::store(new SalesExportExcel($data),$filename,'public');

            return response()->json(['status'=>true, 'url'=>asset('storage')."/".$filename]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

    public function ordersIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $start_date = isset($_GET['start'])?$_GET['start']:'';
            if($start_date){
                $end_date = $_GET['end'];
            }else{
                $start = date('Y-m-d', strtotime('-1 months'));
                $start_date = date('Y-m-d', strtotime($start . ' +1 day'));
                $end_date = date('Y-m-d 23:59:59');
            }
            $orders = Order::where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->get();
            $home_order_count = 0;
            $delivery_order_count = 0;
            $home_order_total = 0;
            $delivery_order_total = 0;
            foreach ($orders as $one){
                $table = Table::find($one->assigned_table_id);
                $product = Product::find($one->product_id);
                $amount = $product->sale_price * $one->order_count;
                if($table->type == "real"){
                    $home_order_count ++;
                    $home_order_total += $amount;
                }else{
                    $delivery_order_count ++;
                    $delivery_order_total += $amount;
                }
            }

            return view('resAdmin.statistics.orders', compact('restaurant', 'start_date', 'end_date', 'home_order_count', 'home_order_total', 'delivery_order_count', 'delivery_order_total'));
        }else{
            abort(404);
        }
    }

    public function bestProductIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $categories = Category::where('restaurant_id', $resId)->get();

            return view('resAdmin.statistics.best-products', compact('restaurant', 'categories'));
        }else{
            abort(404);
        }
    }

    public function bestProductData(Request $request){
        $resId = session()->get('resId');
        if ($resId){
            $start_date = $request->start_date;
            $end_date = $request->end_date;
            $categoryId = $request->category;
            $result = $this->statisticsService->getBestProducts($resId, $start_date, $end_date, $categoryId);

            return response()->json($result);
        }else{
            return response()->json(array());
        }
    }

    public function bestProductExport(Request $request){
        $resId = session()->get('resId');
        if ($resId){
            $data = array(
                'resId' => $resId,
                'start_date' => $request->start_date,
                'end_date' => $request->end_date,
                'categoryId' => $request->category
            );

            $filename = __('best_selling_product').'_'.date('ymshis').'.xlsx';
            Excel::store(new BestProductsExportExcel($data), $filename,'public');

            return response()->json(['status'=>true, 'url'=>asset('storage')."/".$filename]);
        }else{
            return response()->json(['status'=>false]);
        }
    }

    public function breakdownIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $start_date = isset($_GET['start'])?$_GET['start']:'';
            if($start_date){
                $end_date = $_GET['end'];
            }else{
                $start = date('Y-m-d', strtotime('-1 months'));
                $start_date = date('Y-m-d', strtotime($start . ' +1 day'));
                $end_date = date('Y-m-d 23:59:59');
            }
            $waiters = User::where('role', 'waiter')->where('restaurant_id', $resId)->count();
            $tables = Table::where('restaurant_id', $resId)->where('type','real')->count();
            $products = Product::where('restaurant_id', $resId)->where('status', 1)->count();

            $sales = Payment::where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->sum('consumption');
            $tips = Payment::where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->sum('tip');
            $shipping = Payment::where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->sum('shipping');

            return view('resAdmin.statistics.breakdown', compact('restaurant', 'waiters', 'tables', 'products', 'sales', 'tips','shipping', 'start_date', 'end_date'));
        }else{
            abort(404);
        }
    }
}
