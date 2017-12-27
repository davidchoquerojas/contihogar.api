<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\layeredPriceIndex;

class LayeredPriceIndexController extends Controller
{
    private $id_currency = 1;
    private $id_shop = 1;
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

    public function save($id_product,$price,$isNew){

        $mLayeredPriceIndex = NULL;
        if(!$isNew)
            $mLayeredPriceIndex = layeredPriceIndex::where('id_product','=',$id_product)->first(); //PlayeredPriceIndex::find($id);
        else
            $mLayeredPriceIndex = new layeredPriceIndex();

        $mLayeredPriceIndex->id_product = $id_product;
        $mLayeredPriceIndex->id_currency = $this->id_currency;
        $mLayeredPriceIndex->id_shop = $this->id_shop;
        $mLayeredPriceIndex->price_min = floor($price);
        $mLayeredPriceIndex->price_max = round($price);
        $mLayeredPriceIndex->save();
    }
}
