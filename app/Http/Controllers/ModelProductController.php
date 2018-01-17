<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models;
use App\ModelProduct;

class ModelProductController extends Controller
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
     * Inserta o actualiza los ProductModel
     * 
     * @param App\ModelProduct oModelos
     * @param int $id_product
     * @param boolean isNew
     * @return "void"
     */
    public function save($oModelos,$id_product,$isNew){

        if(!$isNew){
            ModelProduct::where('id_product', '=', $id_product)->delete();
        }
        foreach($oModelos as $oModelo){
            if(intval($oModelo["id_model"]) == 0){
                $mModels = new Models();
                $mModels->nombre = $oModelo["model"]["nombre"];
                $mModels->save();
                $oModelo["id_model"] = $mModels->id_model;
            }
            $mModelProduct = new ModelProduct();
            $mModelProduct->id_model = $oModelo["id_model"];
            $mModelProduct->id_product = $id_product;
            $mModelProduct->save();
        }
    }
}
