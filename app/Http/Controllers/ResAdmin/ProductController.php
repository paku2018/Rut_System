<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Product;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class ProductController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $products = Product::where('restaurant_id',$resId)->get();

            return view('resAdmin.product.index',compact('products','restaurant'));
        }else{
            abort(404);
        }
    }

    public function create(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $categories = Category::where('restaurant_id', $resId)->get();

            return view('resAdmin.product.edit', compact('restaurant', 'categories'));
        }else{
            abort(404);
        }
    }

    public function edit($id){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $categories = Category::where('restaurant_id', $resId)->get();
            $data = Product::find($id);

            return view('resAdmin.product.edit',compact('data','restaurant', 'categories'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request){
        try{
            $data = $request->only('id','category_id','name','desc','purchase_price','sale_price', 'status');
            $data['restaurant_id'] = session()->get('resId');
            if (!isset($request->status))
                $data['status'] = 0;
            if($request->hasFile('image')){
                $image = $request->image->store('product','public');
                $data['image'] = asset('storage')."/".$image;
            }
            $result = Product::updateOrCreate(['id'=>$data['id']], $data);

            return Redirect::route('restaurant.products.list');
        }catch (\Exception $e){
            session()->flash('server_error',true);
            return Redirect::back();
        }
    }

    public function delete(Request $request){
        try{
            $product = Product::find($request->id);
            $result = $product->delete();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("product delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }

    }

    public function change(Request $request){
        try{
            $product = Product::find($request->id);
            $product->status = $request->status;
            $product->save();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("product delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }

    }
}
