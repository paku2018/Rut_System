<?php

namespace App\Http\Controllers\ResAdmin;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use App\Models\Restaurant;
use App\Models\Table;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function salesIndex(){
        $resId = session()->get('resId');
        if ($resId){
            $restaurant = Restaurant::find($resId);

            return view('resAdmin.statistics.sales', compact('restaurant'));
        }else{
            abort(404);
        }
    }

    public function getSalesData(Request $request){
        $resId = session()->get('resId');
        if ($resId){
            $start_date = $request->start_date;
            $end_date = $request->end_date;

            $result = Payment::with('restaurant', 'table')->where('restaurant_id', $resId)->where('created_at', '>=', $start_date)->where('created_at', '<=', $end_date)->get();

            return response()->json($result);
        }else{
            return response()->json(array());
        }
    }
}
