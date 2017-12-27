<?php

namespace App\Http\Controllers;
use Carbon\Carbon;

use Illuminate\Http\Request;
use App\ProductAttributeShop;

class ProductAttributeShopController extends Controller
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
    public function create($id_product,$id_product_attribute,$id_shop)
    {
        //
        if(ProductAttributeShop::where('id_product','=',$id_product)->exists()){
            $mProductAttributeShop = new ProductAttributeShop();
            $mProductAttributeShop->id_product = $id_product;
            $mProductAttributeShop->id_product_attribute = $id_product_attribute;
            $mProductAttributeShop->id_shop = $id_shop;
            $mProductAttributeShop->default_on = NULL;
            $mProductAttributeShop->available_date = Carbon::now();
            $mProductAttributeShop->save();
        }else{
            $mProductAttributeShop = new ProductAttributeShop();
            $mProductAttributeShop->id_product = $id_product;
            $mProductAttributeShop->id_product_attribute = $id_product_attribute;
            $mProductAttributeShop->id_shop = $id_shop;
            $mProductAttributeShop->default_on = 1;
            $mProductAttributeShop->available_date = Carbon::now();
            $mProductAttributeShop->save();
        }
        
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
