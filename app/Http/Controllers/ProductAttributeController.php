<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\ProductAttributeImage;
use App\Attribute;
use App\ProductAttribute;
use App\ProductAttributeCombination;
use App\Image;
use App\ImageShop;
use App\ImageLang;

use App\StockAvailable;
use App\ProductAttributeShop;

use DB;

class ProductAttributeController extends Controller
{
    private $id_shop = 1;
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
        //var_dump("aqui_primero");
        $oAttribute = $request["Attribute"];

        $mAttribute = new AttributeController();
        $oAttribute = $mAttribute->save($oAttribute);

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
        $mProductAttributeCombination->id_attribute = $oAttribute->id_attribute;
        $mProductAttributeCombination->id_product_attribute = $mProductAttribute->id_product_attribute;
        $mProductAttributeCombination->save();

        $mStockAvailable = new StockAvailableController();
        $mStockAvailable->create($mProductAttribute);

        $mProductAttributeShop = new ProductAttributeShopController();
        $mProductAttributeShop->create($mProductAttribute->id_product,$mProductAttribute->id_product_attribute,$this->id_shop);

        return response()->json($mProductAttribute, 200);
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
        $oAttributes =  $this->listProductAttributeByProduct($id_product);
        //var_dump($oAttributes);
        foreach($oAttributes as $key=>$oAttribute){
            $oProductImages = ProductAttributeImage::where('id_product_attribute','=',$oAttribute->id_product_attribute)->get();
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
        $id_product_attribute = $id;

        $ProductAttributeImages = ProductAttributeImage::where('id_product_attribute','=',$id_product_attribute)->get();
        foreach ($ProductAttributeImages as $key => $mProductAttributeImage) {
            Image::where('id_image','=',$mProductAttributeImage->id_image)->delete();
            ImageShop::where('id_image','=',$mProductAttributeImage->id_image)->delete();
            ImageLang::where('id_image','=',$mProductAttributeImage->id_image)->delete();
            ProductAttributeImage::where('id_image','=',$mProductAttributeImage)->delete();
        }
        StockAvailable::where('id_product_attribute','=',$id_product_attribute)->delete();
        ProductAttributeShop::where('id_product_attribute','=',$id_product_attribute)->delete();
        ProductAttributeCombination::where('id_product_attribute','=',$id_product_attribute)->delete();
        ProductAttribute::destroy($id_product_attribute);

        return response()->json(array("res"=>true), 200);     
        
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
    private function listProductAttributeByProduct($id_product){
        return DB::table('contihogar_product_attribute_combination')
                ->leftJoin('contihogar_product_attribute', 'contihogar_product_attribute_combination.id_product_attribute', '=','contihogar_product_attribute.id_product_attribute')
                ->leftJoin('contihogar_attribute', 'contihogar_product_attribute_combination.id_attribute', '=', 'contihogar_attribute.id_attribute')
                ->leftJoin('contihogar_attribute_lang','contihogar_attribute_lang.id_attribute','=','contihogar_attribute.id_attribute')
                ->where('contihogar_product_attribute.id_product', '=', $id_product)
                ->where('contihogar_attribute.id_attribute_group', '=', $this->id_attribute_group)
                ->select(
                    'contihogar_product_attribute.id_product_attribute',
                    'contihogar_attribute.id_attribute',
                    'contihogar_attribute.color',
                    'contihogar_product_attribute.id_product',
                    'contihogar_attribute_lang.name'
                )->get();
    }

    public function getProductAttributeByIdProduct(Request $request){
        return $this->listProductAttributeByProduct($request["id_product"]);
    }
}
