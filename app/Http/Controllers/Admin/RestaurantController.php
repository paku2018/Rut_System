<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\RestaurantManger;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class RestaurantController extends Controller
{
    public function index(){
        $restaurants = Restaurant::all();

        return view('admin.restaurant.index',compact('restaurants'));
    }

    public function create(){
        $users = User::where('role', 'restaurant')->get();
        return view('admin.restaurant.edit', compact('users'));
    }

    public function store(Request $request){
        try{
            $res_id = $request->id;
            $data = array(
                'tax_id' => 0,
                //'tax_id' => $request->tax_id,
                'owner_id' => $request->owner_id,
                'name' => $request->restaurant_name,
                'rut' => $request->rut,
                'slogan' => $request->slogan,
                'address' => $request->address,
                'bank_transfer_details' => $request->bank_transfer_details,
            );
            $restaurant = Restaurant::updateOrCreate(['id'=>$res_id], $data);

            $users = $request->users?$request->users:array();
            $users[] = $request->owner_id;
            if($res_id > 0){
                RestaurantManger::where('restaurant_id', $res_id)->whereNotIn('user_id', $users)->delete();
            }
            foreach ($users as $user){
                $data = array(
                    'user_id' => $user,
                    'restaurant_id' => $restaurant->id
                );
                RestaurantManger::updateOrCreate($data, $data);
            }

            return Redirect::route('admin.restaurant.list');
        }catch (\Exception $e){
            session()->flash('server_error',true);
            return Redirect::back();
        }
    }

    public function edit($id){
        $data = Restaurant::find($id);
        $users = User::where('role', 'restaurant')->get();
        $admins = RestaurantManger::where('restaurant_id', $id)->pluck('user_id')->toArray();

        return view('admin.restaurant.edit', compact('data','users','admins'));
    }

    public function delete(Request $request){
        $res_id = $request->id;
        try{
            RestaurantManger::where('restaurant_id', $res_id)->delete();
            $restaurant = Restaurant::find($res_id);
            $result = $restaurant->delete();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("restaurant delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }
    }
}
