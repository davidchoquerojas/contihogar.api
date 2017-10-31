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

class ProductController extends Controller
{
    public $id_lang = 2;
    public $id_shop = 1;
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
        try{
            // Grabar Productos
            $oProduct = $request->all();

            $mProduct = new Product();
            $mProduct->id_supplier = $oProduct["Product"]["id_supplier"];
            $mProduct->id_manufacturer = $oProduct["Product"]["id_manufacturer"];
            $mProduct->id_category_default = 0;
            $mProduct->id_shop_default = 1;
            $mProduct->id_tax_rules_group = 1;
            $mProduct->on_sale = 1;
            $mProduct->online_only = 0;
            $mProduct->ean13 = null;
            $mProduct->isbn = null;
            $mProduct->upc = null;
            $mProduct->ecotax = 0;
            $mProduct->quantity = $oProduct["Product"]["quantity"];
            $mProduct->minimal_quantity = 1;
            $mProduct->price = 0;
            $mProduct->wholesale_price = 0;
            $mProduct->unity = null;
            $mProduct->unit_price_ratio = 0;
            $mProduct->additional_shipping_cost = 0;
            $mProduct->reference = $oProduct["Product"]["reference"];
            $mProduct->supplier_reference = null;
            $mProduct->location = null;
            $mProduct->width = 0;
            $mProduct->height = 0;
            $mProduct->depth = 0;
            $mProduct->weight = 0;
            $mProduct->out_of_stock = 0;
            $mProduct->quantity_discount = 0;
            $mProduct->customizable =
            $mProduct->uploadable_files =
            $mProduct->text_fields =
            $mProduct->active = 0;
            $mProduct->redirect_type = "404";
            $mProduct->id_type_redirected = 0;
            $mProduct->available_for_order = 1;
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

            //Grabar CategoryProduct
            $listCategoryProduct = $oProduct["Product"]["CategoryProduct"];
            $this->grabarProductCategory($listCategoryProduct,$id_product,true);

            //Grabar ProductEvent
            $oProductEvent = $oProduct["Product"]["ProductEvent"];
            $mProductEvent = new ProductEvent();
            //$mProductEvent->id_product_event = ;
            $mProductEvent->id_product = $mProduct->id_product;;
            $mProductEvent->price_start_date = Carbon::parse($oProductEvent["price_start_date"]);
            $mProductEvent->price_end_date = Carbon::parse($oProductEvent["price_end_date"]);
            $mProductEvent->price_impact = $oProductEvent["price_impact"];
            $mProductEvent->cost_impact = $oProductEvent["cost_impact"];
            $mProductEvent->event_price = $oProductEvent["event_price"];
            $mProductEvent->event_cost = $oProductEvent["event_cost"];
            $mProductEvent->cost_start_date = Carbon::parse($oProductEvent["cost_start_date"]);
            $mProductEvent->cost_end_date = Carbon::parse($oProductEvent["cost_end_date"]);
            $mProductEvent->tax_price_impact = $oProductEvent["tax_price_impact"];
            $mProductEvent->tax_cost_impact = $oProductEvent["tax_cost_impact"];
            $mProductEvent->save();

            //Agregar Product Item
            $oProductItems = $oProduct["Product"]["ProductItem"];
            $this->grabarProductItem($oProductItems,$mProduct->id_product,true);
            //Grabar Modelo y Modelo Producto
            $oModelos = $oProduct["Product"]["ModelProduct"];
            $this->grabarProductModel($oModelos,$mProduct->id_product,true);
            //Grabar Categoria Cross
            $oProductCrossCategorys = $oProduct["Product"]["ProductCrossCategory"];
            $this->grabarProductCrossCategory($oProductCrossCategorys,$id_product,true);

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
        $oProduct = Product::find($id);
        $oProduct["ProductLang"] = ProductLang::get()->where('id_product','=',$oProduct->id_product)->first();
        $oProduct["CategoryProduct"] = CategoryProduct::get()->where('id_product','=',$oProduct->id_product);
        $oProduct["ProductEvent"] = ProductEvent::get()->where('id_product','=',$oProduct->id_product)->first();
        $oProduct["ProductItem"] = ProductItem::get()->where('id_product','=',$oProduct->id_product);
        foreach($oProduct["ProductItem"] as $key=>$oProductItem){
            $oProduct["ProductItem"][$key]["ProductItemCaracteristica"] = ProductItemCaracteristica::get()->where('id_product_item','=',$oProductItem["id_product_item"]);
            $oProduct["ProductItem"][$key]["ProductItemShipping"] = ProductItemShipping::get()->where('id_product_item','=',$oProductItem["id_product_item"]);
        }
        $oProduct["ModelProduct"] = ModelProduct::get()->where('id_product','=',$oProduct->id_product);
        foreach($oProduct["ModelProduct"] as $key=>$oModelProduct){
            $oProduct["ModelProduct"][$key]["model"] = Models::get()->where('id_model','=',$oModelProduct["id_model"])->first();
        }

        $oProduct["ProductCrossCategory"] = ProductCrossCategory::get()->where('id_product','=',$oProduct->id_product);
        foreach($oProduct["ProductCrossCategory"] as $key=>$oProductCrossCategory){
            $oProduct["ProductCrossCategory"][$key]["Category"] = Category::with("CategoryLang")->where('id_category','=',$oProductCrossCategory["id_categoria"])->first();
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
        $mProduct->save();
        

        //Actualizar ProductLang
        $oProductLang = $oProduct["ProductLang"];
        $this->grabarProductLang($oProductLang,$id_product,false);

        //Actuaizar Category Product
        $listCategoryProduct = $oProduct["CategoryProduct"];
        $this->grabarProductCategory($listCategoryProduct,$id_product,false);

        //Actualizar Product Event
        $oProductEvent = $oProduct["ProductEvent"];
        $mProductEvent = ProductEvent::find($oProductEvent["id_product_event"]);
        $mProductEvent->price_start_date = Carbon::parse($oProductEvent["price_start_date"]);
        $mProductEvent->price_end_date = Carbon::parse($oProductEvent["price_end_date"]);
        $mProductEvent->price_impact = $oProductEvent["price_impact"];
        $mProductEvent->cost_impact = $oProductEvent["cost_impact"];
        $mProductEvent->event_price = $oProductEvent["event_price"];
        $mProductEvent->event_cost = $oProductEvent["event_cost"];
        $mProductEvent->cost_start_date = Carbon::parse($oProductEvent["cost_start_date"]);
        $mProductEvent->cost_end_date = Carbon::parse($oProductEvent["cost_end_date"]);
        $mProductEvent->tax_price_impact = $oProductEvent["tax_price_impact"];
        $mProductEvent->tax_cost_impact = $oProductEvent["tax_cost_impact"];
        $mProductEvent->save();


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
            $mProductCrossCategory->id_product =$mProduct->id_product; ;
            $mProductCrossCategory->id_categoria = $oProductCrossCategory["id_categoria"];
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
