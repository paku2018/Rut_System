<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use App\Models\Restaurant;
use App\Models\User;
use App\Models\UserHasPermission;
use Illuminate\Http\Request;

class PermissionController extends Controller
{
    public function index(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $users = User::where('role', 'member')->where('restaurant_id',$resId)->orderBy('id', 'desc')->get();

            return view('resAdmin.permission.index',compact('users','restaurant'));
        }else{
            abort(404);
        }
    }

    public function edit(User $user) {
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);
            $permissions = Permission::all();
            $has_permissions = UserHasPermission::where('user_id', $user->id)->pluck('permission_id')->toArray();

            return view('resAdmin.permission.edit', compact('restaurant', 'user', 'permissions', 'has_permissions'));
        }else{
            abort(404);
        }
    }

    public function store(Request $request) {
        try {
            $permissions = $request->permission;
            $user_id = $request->user_id;
            UserHasPermission::where('user_id', $user_id)->delete();
            if ($permissions) {
                foreach ($permissions as $one) {
                    UserHasPermission::create([
                        'user_id' => $user_id,
                        'permission_id' => $one
                    ]);
                }
            }

            return redirect()->route('restaurant.permission.index');
        }catch (\Exception $e) {
            session()->flash('server_error', 1);
            return redirect()->back();
        }
    }
}
