<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\CategoryProduct;

class CategoryProductController extends Controller
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
     * Inserta o actualiza ProductCategory
     * 
     * @param App\CategoryProduct $listCategoryProduct
     * @param int $id_product 
     * @param bool $isNew
     * @return void
     */
    public function save($listCategoryProduct,$id_product,$isNew){
        if(!$isNew) {
            CategoryProduct::where('id_product', '=', $id_product)->delete();
        }
        foreach($listCategoryProduct as $key => $oCategoryProduct){
            $mCategoryProduct = new CategoryProduct();
            $mCategoryProduct->id_category = $oCategoryProduct["id_category"];
            $mCategoryProduct->id_product =  $id_product;
            $mCategoryProduct->position = $key;
            $mCategoryProduct->save();
        }
    }
}
