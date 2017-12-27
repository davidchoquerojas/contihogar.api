<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductEvent;

use Carbon\Carbon;
class ProductEventController extends Controller
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

    /**
     * Graba o actualiza el product Event
     * 
     * @param App\ProductEvent $oProductEvent
     * @return void
     */
    public function save($oProductEvent,$id_product,$isNew){
        //
        $id = $oProductEvent["id_product_event"];
        $mProductEvent = NULL;
        if(!$isNew)
            $mProductEvent = ProductEvent::find($id);
        else
            $mProductEvent = new ProductEvent();

        //var_dump($mProductEvent);
        $mProductEvent->id_product = $id_product;
        $mProductEvent->price_start_date = Carbon::parse($oProductEvent["price_start_date"]);
        $mProductEvent->price_end_date = Carbon::parse($oProductEvent["price_end_date"]);
        $mProductEvent->price_impact = $oProductEvent["price_impact"];
        $mProductEvent->cost_impact = $oProductEvent["cost_impact"];
        $mProductEvent->event_price = $oProductEvent["event_price"];
        $mProductEvent->event_cost = $oProductEvent["event_cost"];
        $mProductEvent->cost_start_date = Carbon::parse($oProductEvent["cost_start_date"]);
        $mProductEvent->cost_end_date = Carbon::parse($oProductEvent["cost_end_date"]);
        $mProductEvent->tax_price_impact = $oProductEvent["tax_price_impact"];
        $mProductEvent->tax_cost_impact = $oProductEvent["tax_cost_impact"];
        $mProductEvent->save();
    }
}
