<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductShop;

use Carbon\Carbon;

class ProductShopController extends Controller
{
    private $id_lang = 2;
    private $id_shop = 1;
    private $id_attribute_group = 3;
    private $id_tax_rules_group = 1;
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

    public function save($oProduct,$isNew){
        $mProductShop = NULL;
        if(!$isNew)
            $mProductShop = ProductShop::where('id_product','=',$oProduct->id_product)->first();
        else
            $mProductShop = new ProductShop();
        
        $mProductShop->id_product = $oProduct->id_product;
        $mProductShop->id_shop = $this->id_shop;
        $mProductShop->id_tax_rules_group = $this->id_tax_rules_group;
        $mProductShop->price = $oProduct->price;
        $mProductShop->wholesale_price = $oProduct->wholesale_price;
        //$mProductShop->
        $mProductShop->date_add = Carbon::now();
        $mProductShop->date_upd = Carbon::now();
        $mProductShop->save();
    }
}
