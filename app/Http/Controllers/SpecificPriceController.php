<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\SpecificPrice;

class SpecificPriceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function save($id_product,$id_shop,$descount,$from,$to,$isNew){
        //
        if($from == NULL || $to == NULL){
            $to = Carbon::minValue();
            $from = Carbon::minValue();
        }  
        
        $mSpecificPrice = NULL;
        if(!$isNew)
            $mSpecificPrice = SpecificPrice::where('id_product','=',$id_product)->first();
        else
            $mSpecificPrice = new SpecificPrice();

        $mSpecificPrice->id_specific_price_rule = 0;
        $mSpecificPrice->id_cart = 0;
        $mSpecificPrice->id_product = $id_product;
        $mSpecificPrice->id_shop = $id_shop;
        $mSpecificPrice->id_shop_group = 0;
        $mSpecificPrice->id_currency = 0;
        $mSpecificPrice->id_country = 0;
        $mSpecificPrice->id_group = 0;
        $mSpecificPrice->id_customer = 0;
        $mSpecificPrice->id_product_attribute = 0;
        $mSpecificPrice->price = -1;
        $mSpecificPrice->from_quantity = 1;
        $mSpecificPrice->reduction = $descount;
        $mSpecificPrice->reduction_type = "percentage";
        $mSpecificPrice->from = $from;
        $mSpecificPrice->to = $to;
        $mSpecificPrice->save();
    }
}
