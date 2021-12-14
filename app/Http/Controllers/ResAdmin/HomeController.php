<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantManger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    public function index(){
        $restaurants = RestaurantManger::where('user_id', Auth::id())->count();
        return view('resAdmin.home', compact('restaurants'));
    }

    public function qrcode(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $code = generateRandomNumber(7).$resId.generateRandomNumber(3);

            return view('resAdmin.qrcode',compact('restaurant','code'));
        }else{
            abort(404);
        }
    }
}
