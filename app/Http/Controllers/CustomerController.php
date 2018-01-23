<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Customer;

class CustomerController extends Controller
{
    //
    public function show($id){
        $customer = Customer::find($id);
        return response()->json($customer, 200);
    }
}
