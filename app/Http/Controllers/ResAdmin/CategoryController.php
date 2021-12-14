<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Restaurant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class CategoryController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $categories = Category::where('restaurant_id',$resId)->get();

            return view('resAdmin.category.index',compact('categories','restaurant'));
        }else{
            abort(404);
        }
    }

    public function create(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.category.edit', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function edit($id){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $data = Category::find($id);

            return view('resAdmin.category.edit',compact('data','restaurant'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request){
        try{
            $data = $request->only('id','name');
            $data['restaurant_id'] = session()->get('resId');
            $result = Category::updateOrCreate(['id'=>$data['id']], $data);

            return Redirect::route('restaurant.categories.list');
        }catch (\Exception $e){
            session()->flash('server_error',true);
            return Redirect::back();
        }
    }

    public function delete(Request $request){
        try{
            $category = Category::find($request->id);
            $result = $category->delete();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("category delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }

    }
}
