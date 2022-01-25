<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;

class UserController extends Controller
{
    public function index(){
        $users = User::where('role','restaurant')->orderBy('id', 'desc')->get();

        return view('admin.user.index',compact('users'));
    }

    public function create(){
        return view('admin.user.edit');
    }

    public function edit($id){
        $data = User::find($id);

        return view('admin.user.edit',compact('data'));
    }

    public function store(Request $request){
        try{
            $data = $request->only('id','name','email','taco_user_id','status');
            if($data['id']==0){
                $data['role'] = "restaurant";
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

            return Redirect::route('admin.user.list');
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
