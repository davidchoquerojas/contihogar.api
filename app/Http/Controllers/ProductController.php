<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;

use App\Product;
use App\ProductLang;
use App\CategoryProduct;
use App\ProductItem;
use App\ProductItemShipping;
use App\ProductItemCaracteristica;

use App\Models;
use App\ModelProduct;
use App\ProductCrossCategory;
use App\Category;
use App\SupplierCostShipping;

use DB;
use File;
use Excel;


class ProductController extends Controller
{
    public $id_lang = 2;
    public $id_shop = 1;
    public $id_attribute_group = 3;
    private $id_tax_rules_group = 1;
    private $tax = 1.18;
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
            //var_dump("aqui");
            $oProduct = $request->all();
            $oProductEvent = $oProduct["Product"]["ProductEvent"];
            $oCategoryProduct = $oProduct["Product"]["CategoryProduct"];

            $mProduct = new Product();
            $mProduct->id_supplier = $oProduct["Product"]["id_supplier"];
            $mProduct->id_manufacturer = $oProduct["Product"]["id_manufacturer"];
            $mProduct->id_category_default = (isset($oCategoryProduct[0]["id_category"]))?$oCategoryProduct[0]["id_category"]:0;
            $mProduct->id_shop_default = 1;
            $mProduct->id_tax_rules_group = 1;
            $mProduct->on_sale = 1;
            $mProduct->quantity = $oProduct["Product"]["quantity"];
            $mProduct->price = (($oProductEvent["price_start_date"] == NULL)? $oProductEvent["price_impact"] :$oProductEvent["event_price"]) / $this->tax;
            $mProduct->wholesale_price = ($oProductEvent["cost_start_date"] == NULL)? $oProductEvent["cost_impact"]:$oProductEvent["event_cost"];
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
            $oProductShop = new ProductShopController();
            $oProductShop->save($mProduct,true);

            $oSpecificPrice = new SpecificPriceController();
            $oSpecificPrice->save($mProduct->id_product,$this->id_shop,$oProductEvent["tax_price_impact"],$oProductEvent["price_start_date"],$oProductEvent["price_end_date"],true);

            $oLayeredPriceIndex = new LayeredPriceIndexController();
            $oLayeredPriceIndex->save($mProduct->id_product,$mProduct->price,true);

            //Grabar CategoryProduct
            $mCategoryProduct = new CategoryProductController();
            $mCategoryProduct->save($oCategoryProduct,$mProduct->id_product,true);

            //Grabar ProductEvent
            $mProductEvent = new ProductEventController();
            $mProductEvent->save($oProductEvent,$mProduct->id_product,true);

            //Agregar Product Item
            $oProductItems = $oProduct["Product"]["ProductItem"];
            $this->grabarProductItem($oProductItems,$mProduct->id_product,true);

            //Grabar Modelo y Modelo Producto
            $mModelProduct = new ModelProductController();
            $mModelProduct->save($oProduct["Product"]["ModelProduct"],$mProduct->id_product,true);

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
        if($oProduct == NULL) return response()->json($oProduct, 200);
        
        $oProduct["ProductLang"] = ProductLang::where('id_product','=',$oProduct->id_product)->first();
        $oProduct["ProductEvent"] = \App\ProductEvent::where('id_product','=',$oProduct->id_product)->first();
        $oProduct["CategoryProduct"] = CategoryProduct::where('id_product','=',$oProduct->id_product)->get();
        $oProduct["ProductItem"] = ProductItem::where('id_product','=',$oProduct->id_product)->get();
        foreach($oProduct["ProductItem"] as $key=>$oProductItem){
            $oProduct["ProductItem"][$key]["ProductItemCaracteristica"] = ProductItemCaracteristica::where('id_product_item','=',$oProductItem["id_product_item"])->orderBy('orden','ASC')->get();
            $oProduct["ProductItem"][$key]["ProductItemShipping"] = ProductItemShipping::where('id_product_item','=',$oProductItem["id_product_item"])->get();
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
        $oProductEvent =  $oProduct["ProductEvent"];
        $id_product = $id;

        if($oProduct["row_state"] == "change_state"){
            $mProduct = Product::find($id_product);
            $mProduct->active = $oProduct["active"];
            $mProduct->save();

            $mProductShop = new ProductShopController();
            $mProductShop->update($request,$id_product);
            
            return response()->json($mProduct, 200);
        }
        
        $mProduct = Product::find($id_product);
        $mProduct->id_supplier = $oProduct["id_supplier"];
        $mProduct->id_manufacturer = $oProduct["id_manufacturer"];
        $mProduct->quantity = $oProduct["quantity"];
        $mProduct->condition = $oProduct["condition"];
        $mProduct->reference = $oProduct["reference"];
        $mProduct->price = (($oProductEvent["price_start_date"] == NULL)? $oProductEvent["price_impact"] :$oProductEvent["event_price"]) / $this->tax;
        $mProduct->wholesale_price = ($oProductEvent["cost_start_date"] == NULL)? $oProductEvent["cost_impact"]:$oProductEvent["event_cost"];
        $mProduct->save();
        

        //Actualizar ProductLang

        $oProductLang = $oProduct["ProductLang"];
        $this->grabarProductLang($oProductLang,$id_product,false);

        //Actuaizar Category Product
        $mCategoryProduct = new CategoryProductController();
        $mCategoryProduct->save($oProduct["CategoryProduct"],$id_product,false);

        //Actualizar Product Event
        $oProductEvent = new ProductEventController();
        $oProductEvent->save($oProduct["ProductEvent"],$id_product,false);
        
        //$mProductEvent = ProductEvent::find($oProductEvent["id_product_event"]);
        $oProductShop = new ProductShopController();
        $oProductShop->save($mProduct,false);

        $oSpecificPrice = new SpecificPriceController();
        $oSpecificPrice->save($mProduct->id_product,$this->id_shop,$oProductEvent["tax_price_impact"],$oProductEvent["price_start_date"],$oProductEvent["price_end_date"],false);

        $oLayeredPriceIndex = new LayeredPriceIndexController();
        $oLayeredPriceIndex->save($mProduct->id_product,$mProduct->price,false);

        //Actualiza Product Item
        $oProductItems = $oProduct["ProductItem"];
        $this->grabarProductItem($oProductItems,$id_product,false);

        //Actualizar Model Product
        $mModelProduct = new ModelProductController();
        $mModelProduct->save($oProduct["ModelProduct"],$id_product,false);
        //Grabar Categoria Cross
        $oProductCrossCategorys = $oProduct["ProductCrossCategory"];
        $this->grabarProductCrossCategory($oProductCrossCategorys,$id_product,false);
        
        return response()->json($mProduct, 200);
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
                $mProductItemCaracteristica->orden = $oProductItemCaracteristica["orden"];
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
                $mProductItemShipping->cantidad = $oProductItemShipping["cantidad"];
                $mProductItemShipping->save();
            }
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
            $mProductCrossCategory->id_category = $oProductCrossCategory["id_category"];
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
     * Lista los productos
     * 
     * @return App\Product
     */
    public function getProductGrid(Request $request){
        //var_dump($request["_start"]);
        $queryStructure = DB::table('contihogar_product')
                            ->join('contihogar_product_lang','contihogar_product.id_product','=','contihogar_product_lang.id_product')
                            ->leftJoin('contihogar_category','contihogar_product.id_category_default','=','contihogar_category.id_category')
                            ->leftJoin('contihogar_category_lang','contihogar_category.id_category','=','contihogar_category_lang.id_category')
                            ->leftJoin('contihogar_supplier','contihogar_product.id_supplier','=','contihogar_supplier.id_supplier')
                            ->leftJoin('contihogar_manufacturer','contihogar_product.id_manufacturer','=','contihogar_manufacturer.id_manufacturer')
                            ->leftJoin('contihogar_model_product','contihogar_product.id_product','=','contihogar_model_product.id_product')
                            ->leftJoin('contihogar_model','contihogar_model_product.id_model','=','contihogar_model.id_model')
                        ->where('contihogar_product_lang.id_lang','=',$this->id_lang)
                        ->where('contihogar_category_lang.id_lang','=',$this->id_lang);
        //Crear los condiciones acuerdo a los parametros indicados
        if(isset($request["product"]) && $request["product"] != "")
            $queryStructure->where('contihogar_product_lang.name','like','%'.$request["product"].'%');
        if(isset($request["supplier"]) && $request["supplier"] != "")
            $queryStructure->where('contihogar_supplier.name','like','%'.$request["supplier"].'%');
        if(isset($request["manufacturer"]) && $request["manufacturer"] != "")
            $queryStructure->where('contihogar_manufacturer.name','like','%'.$request["manufacturer"].'%');
        if(isset($request["model"]) && $request["model"] != "")
            $queryStructure->where('contihogar_model.nombre','like','%'.$request["model"].'%');
        if(isset($request["category"]) && $request["category"] != "")
            $queryStructure->where('contihogar_category_lang.name','like','%'.$request["category"].'%');
        if(isset($request["reference"]) && $request["reference"] != "")
            $queryStructure->where('contihogar_product.reference','=',$request['reference']);
            
        //Realizar con conteo de cuantos registros se encontraron
        $totalQuery = $queryStructure->count();
        $resultQuery =  $queryStructure->select(
                            'contihogar_product.id_product', 
                            'contihogar_product.reference', 
                            'contihogar_product_lang.name as product', 
                            'contihogar_product.id_supplier',
                            'contihogar_product.price',
                            'contihogar_product.wholesale_price',
                            'contihogar_product.quantity',
                            'contihogar_product.active',
                            DB::raw('contihogar_product.price - contihogar_product.wholesale_price / 100 * 100 as margen'),
                            'contihogar_supplier.name as supplier', 
                            'contihogar_manufacturer.id_manufacturer',
                            'contihogar_manufacturer.name as manufacturer',
                            'contihogar_model_product.id_model',
                            'contihogar_model.nombre as model',
                            'contihogar_category.id_category',
                            'contihogar_category_lang.name as category');
                    if(isset($request["_sort"]))
                        $resultQuery->orderBy($request["_sort"], $request["_order"]);
                    $resultQuery->skip((int)$request["_start"])->take((int)$request["_limit"]);
                    
        $finally = $resultQuery->get();
        $response["data"] = $finally;
        $response["total"] = $totalQuery;

        return response()->json($response,200);
    }
    public function mergeUpload(Request $request){
        if($request->file('excel')){
            $path = $request->file('excel')->getRealPath();
            $data = Excel::load($path, function($reader){})->get();
            $productIds = [];
            if(!empty($data) && $data->count()){
                foreach ($data->toArray() as $row){
                    if(!empty($row))
                    {
                        $productIds[] = $row["id"];
                    }
                }
            }
            $listProduct = $this->listProductsById($productIds);
            
            return response()->json($listProduct, 200);
        }
    }
    public function getProductById(Request $request){
        $listProduct = $this->listProductsById(array((int)$request["id_product"]));
        return response()->json($listProduct,200);
    }

    private function listProductsById($id_products){
        $sqlStructure = DB::table('contihogar_product')
                            ->join('contihogar_product_shop','contihogar_product_shop.id_product','=','contihogar_product.id_product')
                            ->join('contihogar_product_lang','contihogar_product.id_product','=','contihogar_product_lang.id_product')
                            ->join('contihogar_specific_price','contihogar_specific_price.id_product','=','contihogar_product.id_product')
                            ->join('contihogar_product_event','contihogar_product_event.id_product','=','contihogar_product.id_product')
                            ->leftJoin('contihogar_category','contihogar_product.id_category_default','=','contihogar_category.id_category')
                            ->leftJoin('contihogar_category_lang','contihogar_category.id_category','=','contihogar_category_lang.id_category')
                            ->leftJoin('contihogar_supplier','contihogar_product.id_supplier','=','contihogar_supplier.id_supplier')
                            ->leftJoin('contihogar_manufacturer','contihogar_product.id_manufacturer','=','contihogar_manufacturer.id_manufacturer')
                        ->where('contihogar_product_lang.id_lang','=',$this->id_lang)
                        ->where('contihogar_category_lang.id_lang','=',$this->id_lang)
                        ->whereIn('contihogar_product.id_product',$id_products);
        
            $sqlResult =  $sqlStructure->select(
                        'contihogar_product.id_product',
                        'contihogar_product_event.id_product_event',
                        'contihogar_product_shop.with_shipping_cost',
                        'contihogar_product.reference', 
                        'contihogar_product_lang.name as product', 
                        'contihogar_product.id_supplier',
                        'contihogar_product.price',
                        'contihogar_product.wholesale_price',
                        'contihogar_specific_price.reduction',
                        'contihogar_product.quantity',
                        'contihogar_product.active',
                        DB::raw('contihogar_product.price - contihogar_product.wholesale_price / 100 * 100 as margen'),
                        'contihogar_supplier.name as supplier', 
                        'contihogar_manufacturer.id_manufacturer',
                        'contihogar_manufacturer.name as manufacturer',
                        'contihogar_category.id_category',
                        'contihogar_category_lang.name as category',
                        DB::raw('0 as item_sale'),
                        DB::raw('0 as visit'),
                        DB::raw('0 as cost_shipping'),
                        DB::raw('0 as id_state')
                        );
        return $sqlResult->get();
    }
    public function getShippingCostById(Request $request){
        $cost_shipping = 0;
        if(!SupplierCostShipping::where('id_supplier','=',$request["id_supplier"])->where('active','=',1)->where('id_departamento','=',$request["id_state"])->exists()){
            $listProductItemShipping = DB::table('contihogar_product_item_shipping')
                                        ->join('contihogar_product_item','contihogar_product_item.id_product_item','=','contihogar_product_item_shipping.id_product_item')
                                        ->where('contihogar_product_item.id_product','=',$request["id_product"])
                                        ->select('contihogar_product_item_shipping.*')->get();
            
            $oCargaProvincia = DB::table('contihogar_carga_provincia')
                                ->where('contihogar_carga_provincia.id_state','=',$request["id_state"])
                                //->groupBy('contihogar_carga_provincia.id_state')
                                ->select(
                                        'contihogar_carga_provincia.id_state',
                                        DB::raw('AVG(contihogar_carga_provincia.kilo_base_final) as kilo_base_final'),
                                        'contihogar_carga_provincia.kilo_adicional')->first();
            $costoPorFormula = 0;
            $costoPorCategoria = 0;

            if($oCargaProvincia){
                foreach($listProductItemShipping as $oProductItemShipping){
                    $altoAnchoProf = ((int)$oProductItemShipping->alto * (int)$oProductItemShipping->ancho * (int)$oProductItemShipping->profundidad / 6000);
                    $altoAnchoProf = $altoAnchoProf > (int)$oProductItemShipping->peso?$altoAnchoProf:(int)$oProductItemShipping->peso;
                    $costoPorFormula += ($oCargaProvincia->kilo_base_final + ($altoAnchoProf - 1) * floatval($oCargaProvincia->kilo_adicional) * $oProductItemShipping->cantidad);
                    if($oProductItemShipping->id_category_shipping != 0 && $oProductItemShipping->id_category_shipping != 11){
                        //Ejecutar Query
                        $oCarrierCategory = DB::table('contihogar_carrier_category')
                                            ->where('contihogar_carrier_category.id_departamento','=',$request["id_departamento"])
                                            ->where('contihogar_carrier_category.id_category','=',$oProductItemShipping->id_category_shipping)
                                            ->select(
                                                DB::raw('AVG(contihogar_carrier_category.costo) as costo')
                                            )->first();
                        
                        if($oCarrierCategory && $oCarrierCategory->costo != NULL)
                            $costoPorCategoria += floatval($oCarrierCategory->costo);
                    }
                }
                if($costoPorFormula < $costoPorCategoria && $costoPorCategoria > 0)
                    $cost_shipping += $costoPorFormula;
                else
                    $cost_shipping += $costoPorFormula;
    
                if($costoPorCategoria < $costoPorFormula && $costoPorFormula > 0)
                    $cost_shipping += $costoPorCategoria;
                else
                    $cost_shipping += $costoPorCategoria;
            }
        }

        return response()->json(array("cost_shipping"=>$cost_shipping), 200);
    }
}