<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Address;

class AddressController extends Controller
{
    //
    public function show($id){
        $address = Address::find($id);

        return response()->json($address,200);
    }
}
