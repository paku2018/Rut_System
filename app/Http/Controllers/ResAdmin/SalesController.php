<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;

class SalesController extends Controller
{
    public function index() {
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.sales.index', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function getProducts(Request $request) {
        $resId = session()->get('resId');
        if ($resId) {
            $products = Product::with('category')->where('restaurant_id', $resId)->where('status',1)
                ->whereHas('category', function ($query) {
                    $query->where('restaurant_id', '!=', 0);
                });
            $filter = $request->filter;
            if ($filter) {
                $products = $products->where('name', 'like', '%'.$filter.'%');
            }
            $products = $products->get();

            return response()->json($products);
        }else {
            return response()->json(array());
        }
    }
}
