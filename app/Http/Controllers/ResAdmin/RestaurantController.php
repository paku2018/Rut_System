<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantManger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RestaurantController extends Controller
{
    public function index(){
        $user_id = Auth::id();
        $res_ids = RestaurantManger::where('user_id', $user_id)->pluck('restaurant_id')->toArray();
        $restaurants = Restaurant::whereIn('id', $res_ids)->get();

        return view('resAdmin.restaurant.index',compact('restaurants'));
    }

    public function detail($id){
        $restaurant = Restaurant::find($id);
        session()->put('resId', $id);

        return view('resAdmin.restaurant.detail', compact('restaurant'));
    }
}
