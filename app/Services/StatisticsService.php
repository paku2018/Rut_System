<?php


namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\Product;
use App\Models\User;

class StatisticsService
{
    public function getSales($resId, $start_date, $end_date){
        $result = Payment::with('restaurant', 'table')->where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->get();

        return $result;
    }

    public function getBestProducts($resId, $start_date, $end_date, $categoryId = null){
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
                'product_purchase_price' => $product->purchase_price,
                'product_price' => $product->sale_price,
                'ordered_count' => $count
            );
            $result[] = $data;
        }
        usort($result, "cmp");

        return $result;
    }
}
