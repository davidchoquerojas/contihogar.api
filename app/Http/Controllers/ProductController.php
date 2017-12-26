<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Product;
use App\ProductLang;
use App\ProductEvent;
use App\CategoryProduct;
use App\ProductItem;
use App\ProductItemShipping;
use App\ProductItemCaracteristica;

use App\Models;
use App\ModelProduct;
use App\ProductCrossCategory;
use App\Category;

use App\ProductShop;

class ProductController extends Controller
{
    public $id_lang = 2;
    public $id_shop = 1;
    public $id_attribute_group = 3;
    private $id_tax_rules_group = 1;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $oProduct = Product::all();
        return response()->json($oProduct,200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try{
            // Grabar Productos
            $oProduct = $request->all();
            $oProductEvent = $oProduct["Product"]["ProductEvent"];

            $mProduct = new Product();
            $mProduct->id_supplier = $oProduct["Product"]["id_supplier"];
            $mProduct->id_manufacturer = $oProduct["Product"]["id_manufacturer"];
            $mProduct->id_category_default = 0;
            $mProduct->id_shop_default = 1;
            $mProduct->id_tax_rules_group = 1;
            $mProduct->on_sale = 1;
            $mProduct->quantity = $oProduct["Product"]["quantity"];
            $mProduct->price = $oProductEvent["price_impact"];
            $mProduct->wholesale_price = $oProductEvent["cost_impact"];
            $mProduct->additional_shipping_cost = 0;
            $mProduct->reference = $oProduct["Product"]["reference"];
            $mProduct->active = 0;
            $mProduct->redirect_type = "404";
            $mProduct->id_type_redirected = 0;
            //$mProduct->available_date = \DateTime('today');
            $mProduct->show_condition = 0;
            $mProduct->condition = $oProduct["Product"]["condition"];;
            $mProduct->show_price = 1;
            $mProduct->indexed = 0;
            $mProduct->visibility = "both";
            $mProduct->cache_is_pack = 0;
            $mProduct->cache_has_attachments = 0;
            $mProduct->is_virtual = 0;
            $mProduct->cache_default_attribute = 0;
            $mProduct->date_add = Carbon::now();
            $mProduct->date_upd = Carbon::now();
            $mProduct->advanced_stock_management = 0;
            $mProduct->pack_stock_type = 3;
            $mProduct->state = 1;
            $mProduct->nuevo = 1;
            $mProduct->save();

            //Grabar Product_lang
            $oProductLang =  $oProduct["Product"]["ProductLang"];
            $this->grabarProductLang($oProductLang,$mProduct->id_product,true);

            //Product Shop
            $mProductShop = new ProductShop();
            $mProductShop->id_product = $mProduct->id_product;
            $mProductShop->id_shop = $this->id_shop;
            $mProductShop->id_tax_rules_group = $this->id_tax_rules_group;
            //$mProductShop->
            $mProductShop->date_add = Carbon::now();
            $mProductShop->date_upd = Carbon::now();
            $mProductShop->save();

            $oSpecificPrice = new SpecificPrice();
            $oSpecificPrice->save($mProduct->id_product,$this->id_shop,$oProductEvent["tax_price_impact"],'0000-00-00 00:00:00','0000-00-00 00:00:00',true);

            $oLayeredPriceIndex = new LayeredPriceIndex();
            $oLayeredPriceIndex->save($mProduct->id_product,$mProduct->price,true);

            //Grabar CategoryProduct
            $listCategoryProduct = $oProduct["Product"]["CategoryProduct"];
            $this->grabarProductCategory($listCategoryProduct,$mProduct->id_product,true);

            //Grabar ProductEvent
            $oProductEvent = new ProductEvent();
            $oProductEvent->save($oProductEvent,false);

            //Agregar Product Item
            $oProductItems = $oProduct["Product"]["ProductItem"];
            $this->grabarProductItem($oProductItems,$mProduct->id_product,true);
            //Grabar Modelo y Modelo Producto
            $oModelos = $oProduct["Product"]["ModelProduct"];
            $this->grabarProductModel($oModelos,$mProduct->id_product,true);
            //Grabar Categoria Cross
            $oProductCrossCategorys = $oProduct["Product"]["ProductCrossCategory"];
            $this->grabarProductCrossCategory($oProductCrossCategorys,$mProduct->id_product,true);

            return response()->json($mProduct, 200);
        }catch(Exception $e)
        {
            return response()->json($e, 500);
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
        //var_dump($id);
        $oProduct = Product::find($id);
        if($oProduct == NULL) return response()->json(array("NO DATA"), 200);
        
        $oProduct["ProductLang"] = ProductLang::where('id_product','=',$oProduct->id_product)->first();
        $oProduct["ProductEvent"] = ProductEvent::where('id_product','=',$oProduct->id_product)->first();
        $oProduct["CategoryProduct"] = CategoryProduct::where('id_product','=',$oProduct->id_product)->get();
        $oProduct["ProductItem"] = ProductItem::where('id_product','=',$oProduct->id_product)->get();
        foreach($oProduct["ProductItem"] as $key=>$oProductItem){
            $oProduct["ProductItem"][$key]["ProductItemCaracteristica"] = ProductItemCaracteristica::where('id_product_item','=',$oProductItem["id_product_item"])->get();
            $oProduct["ProductItem"][$key]["ProductItemShipping"] = ProductItemShipping::where('id_product_item','=',$oProductItem["id_product_item"])->orderBy('orden','ASC')->get();
        }
        $oProduct["ModelProduct"] = ModelProduct::where('id_product','=',$oProduct->id_product)->get();
        foreach($oProduct["ModelProduct"] as $key=>$oModelProduct){
            $oProduct["ModelProduct"][$key]["model"] = Models::where('id_model','=',$oModelProduct["id_model"])->first();
        }

        $oProduct["ProductCrossCategory"] = ProductCrossCategory::where('id_product','=',$oProduct->id_product)->get();
        foreach($oProduct["ProductCrossCategory"] as $key=>$oProductCrossCategory){
            $oProduct["ProductCrossCategory"][$key]["Category"] = Category::with("CategoryLang")->where('id_category','=',$oProductCrossCategory["id_category"])->first();
        }
        
        return response()->json($oProduct, 200);
        
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
        //Actualizar Producto
        $oProduct = $request;
        $id_product = $id;
        
        $mProduct = Product::find($id_product);
        $mProduct->id_supplier = $oProduct["id_supplier"];
        $mProduct->id_manufacturer = $oProduct["id_manufacturer"];
        $mProduct->quantity = $oProduct["quantity"];
        $mProduct->condition = $oProduct["condition"];
        $mProduct->reference = $oProduct["reference"];
        $mProduct->price = $oProduct["ProductEvent"]['price_impact'];
        $mProduct->save();
        

        //Actualizar ProductLang
        $oProductLang = $oProduct["ProductLang"];
        $this->grabarProductLang($oProductLang,$id_product,false);

        //Actuaizar Category Product
        $listCategoryProduct = $oProduct["CategoryProduct"];
        $this->grabarProductCategory($listCategoryProduct,$id_product,false);

        //Actualizar Product Event
        $oProductEvent = new ProductEvent();
        $oProductEvent->save($oProduct["ProductEvent"],false);
        
        //$mProductEvent = ProductEvent::find($oProductEvent["id_product_event"]);
        

        //Actualiza Product Item
        $oProductItems = $oProduct["ProductItem"];
        $this->grabarProductItem($oProductItems,$id_product,false);

        //Actualizar Model Product
        $oModelos = $oProduct["ModelProduct"];
        $this->grabarProductModel($oModelos,$id_product,false);

        //Grabar Categoria Cross
        $oProductCrossCategorys = $oProduct["ProductCrossCategory"];
        $this->grabarProductCrossCategory($oProductCrossCategorys,$id_product,false);
        
        return response()->json($id, 200);
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
     * inserta o actualiza los productItems
     * 
     * @param App\ProductItem $oProductItems
     * @param int $id_product
     * @param boolean $isNew
     * @return "void 
     */
    public function grabarProductItem($oProductItems,$id_product,$isNew)
    {
        foreach($oProductItems as $key=>$ProductItem){
            $id_product_item = 0;
            if($isNew){
                $mProdcutItem = new ProductItem();
                $mProdcutItem->id_product = $id_product;
                $mProdcutItem->nombre = $ProductItem["nombre"];
                $mProdcutItem->cantidad = $ProductItem["cantidad"];
                $mProdcutItem->descripcion = "";
                $mProdcutItem->save();
                $id_product_item = $mProdcutItem->id_product_item;
            }else{
                $mProdcutItem = ProductItem::find($ProductItem["id_product_item"]);
                $mProdcutItem->nombre = $ProductItem["nombre"];
                $mProdcutItem->cantidad = $ProductItem["cantidad"];
                $mProdcutItem->save();
                $id_product_item = $mProdcutItem->id_product_item;
            }

            //Grabando ProductItemCaracteristica
            if(!$isNew){
                ProductItemCaracteristica::where('id_product_item', '=', $id_product_item)->delete();
            }
            $oProductItemCaracteristicas = $ProductItem["ProductItemCaracteristica"];
            foreach($oProductItemCaracteristicas as $oProductItemCaracteristica){
                $mProductItemCaracteristica = new ProductItemCaracteristica();
                $mProductItemCaracteristica->id_product_item = $id_product_item;
                $mProductItemCaracteristica->nombre = $oProductItemCaracteristica["nombre"];
                $mProductItemCaracteristica->valor = $oProductItemCaracteristica["valor"];
                $mProductItemCaracteristica->campo = $oProductItemCaracteristica["campo"];
                $mProductItemCaracteristica->save();
            }

            //Grabando ProductItemShipping
            if(!$isNew){
                ProductItemShipping::where('id_product_item', '=', $id_product_item)->delete();
            }
            $oProductItemShippings = $ProductItem["ProductItemShipping"];
            foreach($oProductItemShippings as $oProductItemShipping){
                $mProductItemShipping = new ProductItemShipping();
                $mProductItemShipping->id_product_item = $id_product_item;
                $mProductItemShipping->ancho = $oProductItemShipping["ancho"];
                $mProductItemShipping->alto = $oProductItemShipping["alto"];
                $mProductItemShipping->profundidad = $oProductItemShipping["profundidad"];
                $mProductItemShipping->peso = $oProductItemShipping["peso"];
                $mProductItemShipping->orden = $oProductItemShipping["orden"];
                $mProductItemShipping->save();
            }
        }
    }
    /**
     * Inserta o actualiza los ProductModel
     * 
     * @param App\ModelProduct oModelos
     * @param int $id_product
     * @param boolean isNew
     * @return "void"
     */
    public function grabarProductModel($oModelos,$id_product,$isNew){

        if(!$isNew){
            CategoryProduct::where('id_product', '=', $id_product)->delete();
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
    /**
     * Inserta o actualiza los ProductCrossCategory
     * 
     * @param App\ProductCrossCategory $oProductCrossCategorys,
     * @param int $id_product, 
     * @param boolean $isNew
     * @return "void"
     */
    public function grabarProductCrossCategory($oProductCrossCategorys,$id_product,$isNew){
        if(!$isNew){
            ProductCrossCategory::where('id_product', '=', $id_product)->delete();
        }
        foreach($oProductCrossCategorys as $oProductCrossCategory){
            $mProductCrossCategory = new ProductCrossCategory();
            $mProductCrossCategory->id_product = $id_product;
            $mProductCrossCategory->id_category = $oProductCrossCategory["id_categoria"];
            $mProductCrossCategory->save();
        }
    }

    /**
     * Inserta o actualiza ProductLang 
     * 
     * @param App\ProductLang $oProductLang,
     * @param int $id_product, 
     * @param bool $isNew
     * @return "void"
     */
    public function grabarProductLang($oProductLang,$id_product,$isNew){
        if(!$isNew){
            ProductLang::where('id_product', '=', $id_product)->delete();
        }
        $mProductLang = new ProductLang();
        $mProductLang->id_product = $id_product;
        $mProductLang->id_shop = $this->id_shop;
        $mProductLang->id_lang = $this->id_lang;
        $mProductLang->description = $oProductLang["description"];
        $mProductLang->description_short = $oProductLang["description_short"];
        $mProductLang->link_rewrite = $oProductLang["link_rewrite"];
        $mProductLang->meta_description = $oProductLang["meta_description"];
        $mProductLang->meta_keywords = $oProductLang["meta_keywords"];
        $mProductLang->meta_title = $oProductLang["meta_title"];
        $mProductLang->name = $oProductLang["name"];
        $mProductLang->available_now = "";
        $mProductLang->available_later = "";
        $mProductLang->inst_message = $oProductLang["inst_message"];
        $mProductLang->save();
    }

    /**
     * Inserta o actualiza ProductCategory
     * 
     * @param App\CategoryProduct $listCategoryProduct
     * @param int $id_product 
     * @param bool $isNew
     * @return void
     */
    public function grabarProductCategory($listCategoryProduct,$id_product,$isNew){
        if(!$isNew) {
            CategoryProduct::where('id_product', '=', $id_product)->delete();
        }
        foreach($listCategoryProduct as $oCategoryProduct){
            $mCategoryProduct = new CategoryProduct();
            $mCategoryProduct->id_category = $oCategoryProduct["id_category"];
            $mCategoryProduct->id_product =  $id_product;
            $mCategoryProduct->position = 0;
            $mCategoryProduct->save();
        }
    }
}
