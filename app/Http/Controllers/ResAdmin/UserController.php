<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $users = User::whereIn('role',['waiter','member'])->where('restaurant_id',$resId)->orderBy('id', 'desc')->get();

            return view('resAdmin.user.index',compact('users','restaurant'));
        }else{
            abort(404);
        }
    }

    public function create(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.user.edit', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function edit($id){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $data = User::find($id);

            return view('resAdmin.user.edit',compact('data','restaurant'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request){
        try{
            $data = $request->only('id','name','email','role');
            $data['restaurant_id'] = session()->get('resId');
            if($data['id']==0){
                $data['email_verified_at'] = date('Y-m-d H:i:s');

                $email = User::where('email',$data['email'])->first();
                if($email!=null){
                    session()->flash('email_error',true);
                    return Redirect::back();
                }
                $data['password'] = Hash::make($request->password);
            }elseif(isset($request->change_password)){
                $data['password'] = Hash::make($request->new_password);
            }

            $result = User::updateOrCreate(['id'=>$data['id']], $data);

            return Redirect::route('restaurant.members.list');
        }catch (\Exception $e){
            session()->flash('server_error',true);
            return Redirect::back();
        }
    }

    public function delete(Request $request){
        try{
            $user = User::find($request->id);
            $result = $user->delete();

            return response()->json(['result'=>true,'success'=>true]);
        }catch (\Exception $e){
            Log::info("user delete error:".$e->getMessage());
            return response()->json(['result'=>false]);
        }

    }
}
