<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Carrier;

class CarrierController extends Controller
{
    //
    private $active = 1;

    public function index(){
        $carriers = Carrier::where('active','=',$this->active)->get();
        
        return response()->json($carriers,200);
    }
}
