<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductAttributeCombination;
use App\Attribute;
use App\AttributeLang;
use App\AttributeShop;
use App\ProductAttribute;

use DB;


class AttributeController extends Controller
{
    private $id_shop = 1;
    private $id_attribute_group = 3;
    private $id_lang = 2;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $listAttribute = DB::table('contihogar_attribute')
                            ->join('contihogar_attribute_lang','contihogar_attribute_lang.id_attribute','=','contihogar_attribute.id_attribute')
                            ->where('contihogar_attribute.id_attribute_group','=',$this->id_attribute_group)
                            ->get();
        
        return response()->json($listAttribute, 200);
        
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
        try{
            Attribute::destroy($oProductAttributeCombination["id_attribute"]);
            return response()->json(array("res"=>true), 200);     
        }catch(Exeption $ex){
            return response()->json($ex, 200);     
        }
    }

    public function save($oAttribute){

        if(!Attribute::where('id_attribute_group','=',$this->id_attribute_group)->where('color','=',trim($oAttribute['color']))->exists()){
            $position = (Attribute::where('id_attribute_group','=',$this->id_attribute_group)->max('position'))+1;

            $mAttribute = new Attribute();
            $mAttribute->id_attribute_group = $this->id_attribute_group;
            $mAttribute->color = $oAttribute["color"];
            $mAttribute->position = $position;
            $mAttribute->save();

            $mAttributeLang = new AttributeLang();
            $mAttributeLang->id_attribute = $mAttribute->id_attribute;
            $mAttributeLang->id_lang = $this->id_lang;
            $mAttributeLang->name = $oAttribute["name"];
            $mAttributeLang->save();
            
            $mAttributeShop = new AttributeShop();
            $mAttributeShop->id_attribute = $mAttribute->id_attribute;
            $mAttributeShop->id_shop = $this->id_shop;
            $mAttributeShop->save();

            return $mAttribute;
        }else{
            return Attribute::where('id_attribute_group','=',$this->id_attribute_group)->where('color','=',trim($oAttribute['color']))->first();
        }
    }
}
