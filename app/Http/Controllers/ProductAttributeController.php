<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductAttributeImage;
use App\Attribute;
use App\ProductAttribute;
use App\ProductAttributeCombination;
use DB;

class ProductAttributeController extends Controller
{
    private $id_attribute_group = 3;
    private $pressta_shop_url;
    private $img_prefix_ext = ".jpg";
    public function __construct(){
        $this->pressta_shop_url = \Config::get('constants.pressta_shop.url');
    }
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
        $oAttribute = $request["Attribute"];
        if(intval($oAttribute["id_attribute"]) > 0){
            $mAttribute = Attribute::find($oAttribute["id_attribute"]);
            $mAttribute->color = $oAttribute["color"];
            $mAttribute->save();
            
            return response()->json($mAttribute, 200);
            
        }else{
            $mAttribute = new Attribute();
            $mAttribute->id_attribute_group = $this->id_attribute_group;
            $mAttribute->color = $oAttribute["color"];
            $mAttribute->position++;
            $mAttribute->save();

            $oProductAttribute = $request["ProductAttribute"];
            $mProductAttribute = new ProductAttribute();
            $mProductAttribute->wholesale_price = $oProductAttribute["wholesale_price"];
            $mProductAttribute->price = $oProductAttribute["price"]; 
            $mProductAttribute->ecotax = $oProductAttribute["ecotax"]; 
            $mProductAttribute->quantity = $oProductAttribute["quantity"]; 
            $mProductAttribute->weight = $oProductAttribute["weight"]; 
            $mProductAttribute->unit_price_impact = $oProductAttribute["unit_price_impact"]; 
            $mProductAttribute->default_on = $oProductAttribute["default_on"]; 
            $mProductAttribute->minimal_quantity = $oProductAttribute["minimal_quantity"]; 
            $mProductAttribute->id_product = $oProductAttribute["id_product"];
            $mProductAttribute->save();
    
            $mProductAttributeCombination = new ProductAttributeCombination();
            $mProductAttributeCombination->id_attribute = $mAttribute->id_attribute;
            $mProductAttributeCombination->id_product_attribute = $mProductAttribute->id_product_attribute;
            $mProductAttributeCombination->save();

            return response()->json($mAttribute, 200);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $id_product = $id;
        //
        $oAttributes = DB::table('contihogar_product_attribute_combination')
                            ->leftJoin('contihogar_product_attribute', 'contihogar_product_attribute_combination.id_product_attribute', '=','contihogar_product_attribute.id_product_attribute')
                            ->leftJoin('contihogar_attribute', 'contihogar_product_attribute_combination.id_attribute', '=', 'contihogar_attribute.id_attribute')
                            ->where('contihogar_product_attribute.id_product', '=', $id_product)
                            ->where('contihogar_attribute.id_attribute_group', '=', $this->id_attribute_group)
                            ->get();
        //var_dump($oAttributes);
        foreach($oAttributes as $key=>$oAttribute){
            $oProductImages = ProductAttributeImage::get()->where('id_product_attribute','=',$oAttribute->id_product_attribute);
            $oListProductImages = array();
            foreach($oProductImages as $oProductImage){
                $oProductImage["src"] = $this->pressta_shop_url."/img/p/".$this->crearRutaImage($oProductImage["id_image"])."/".$oProductImage["id_image"].$this->img_prefix_ext;
                array_push($oListProductImages,$oProductImage);
            }
            $oAttributes[$key]->Images = $oListProductImages;
        }
        
        //var_dump($categoryByParents);
        return response()->json($oAttributes, 200);
        
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
    private function crearRutaImage($id_image){
        $strUrl = "";
        $iLenght = strlen($id_image);
        for($_i = 0;$_i < $iLenght ;$_i++){
            if($iLenght == $_i+1)
                $strUrl.= substr($id_image,$_i,1);
            else
                $strUrl.= substr($id_image,$_i,1)."/";
        }
        return $strUrl;
    }
}
