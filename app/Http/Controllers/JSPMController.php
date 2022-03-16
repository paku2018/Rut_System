<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Category;

class JSPMController extends Controller
{
    public function index(Request $request){

        //SET THE LICENSE INFO
        $license_owner = 'Pago Cash SpA - 1 WebApp Lic - 1 WebServer Lic';
        $license_key = '8268C5B205EB1A76641A814B279E88A074C7308B';

        //DO NOT MODIFY THE FOLLOWING CODE
        $timestamp = request()->query('timestamp');
        $license_hash = hash('sha256', $license_key . $timestamp, false);
        $resp = $license_owner . '|' . $license_hash;

        return response($resp)->header('Content-Type', 'text/plain');

    }

    public function test(Request $request){
        $categories = Category::all();
        return response($categories)->header('Content-Type', 'application/json');
    }

}