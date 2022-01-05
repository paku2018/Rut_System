<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Restaurant;
use App\Models\Table;
use App\Models\User;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
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

            $result = Payment::with('restaurant', 'table')->where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->get();

            return response()->json($result);
        }else{
            return response()->json(array());
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
            $products = Product::where('restaurant_id', $resId)->where('status', 1);
            if ($categoryId)
                $products = $products->where('category_id', $categoryId);
            $products = $products->get();
            $result = array();
            foreach ($products as $product){
                $pId = $product->id;
                $count = Order::where('product_id', $pId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->sum('order_count');

                $data = array(
                    'product_id' => $pId,
                    'product_name' => $product->name,
                    'product_price' => $product->sale_price,
                    'ordered_count' => $count
                );
                $result[] = $data;
            }

            return response()->json($result);
        }else{
            return response()->json(array());
        }
    }

    public function breakdownIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $waiters = User::where('role', 'waiter')->where('restaurant_id', $resId)->count();
            $tables = Table::where('restaurant_id', $resId)->where('type','real')->count();
            $products = Product::where('restaurant_id', $resId)->where('status', 1)->count();

            $sales = Payment::where('restaurant_id', $resId)->sum('consumption');
            $tips = Payment::where('restaurant_id', $resId)->sum('tip');
            $shipping = Payment::where('restaurant_id', $resId)->sum('shipping');

            return view('resAdmin.statistics.breakdown', compact('restaurant', 'waiters', 'tables', 'products', 'sales', 'tips','shipping'));
        }else{
            abort(404);
        }
    }
}
