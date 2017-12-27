<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\StockAvailable;

class StockAvailableController extends Controller
{
    private $id_shop = 1;
    private $id_shop_group = 0;
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
    public function create($oProductAttribute)
    {
        //
        $mStockAvailable = new StockAvailable();
        $mStockAvailable->id_product = $oProductAttribute->id_product;
        $mStockAvailable->id_product_attribute = $oProductAttribute->id_product_attribute;
        $mStockAvailable->id_shop = $this->id_shop;
        $mStockAvailable->id_shop_group = $this->id_shop_group;
        $mStockAvailable->quantity = $oProductAttribute->quantity;
        $mStockAvailable->physical_quantity = $oProductAttribute->quantity;
        $mStockAvailable->reserved_quantity = 0;
        $mStockAvailable->depends_on_stock = 0;
        $mStockAvailable->out_of_stock = 0;
        $mStockAvailable->save();

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
}
