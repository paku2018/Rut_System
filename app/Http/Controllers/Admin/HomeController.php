<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Restaurant;
use App\Models\User;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(){
        $restaurants = Restaurant::all()->count();
        $users = User::where('role','restaurant')->count();

        return view('admin.home', compact('restaurants', 'users'));
    }
}
