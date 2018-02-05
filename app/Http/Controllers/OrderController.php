<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\OrderDetail;

use DB;
use Carbon\Carbon;
class OrderController extends Controller
{
    private $valid = 1;
    private $tax_payout_passarella = 0.95;
    //
    public function getProductTop(){

        $resultTop =  DB::table('contihogar_product')
                        ->join('contihogar_product_lang','contihogar_product_lang.id_product','=','contihogar_product.id_product')
                        ->join('contihogar_product_shop','contihogar_product_shop.id_product','=','contihogar_product.id_product')
                        ->leftJoin('contihogar_order_detail','contihogar_order_detail.product_id','=','contihogar_product.id_product')
                        ->leftJoin('contihogar_orders','contihogar_orders.id_order','=','contihogar_order_detail.id_order')
                    ->where('contihogar_orders.valid','=',$this->valid)
                    //->where('contihogar_category_lang.id_lang','=',$this->id_lang)
                    ->groupBy('contihogar_order_detail.product_id')
                    ->groupBy('contihogar_product_lang.name')
                    ->orderBy(DB::raw('SUM(contihogar_order_detail.product_quantity)'))
                    ->select('contihogar_product.id_product', 
                            'contihogar_product_lang.name', 
                            DB::raw('SUM(contihogar_order_detail.product_quantity) as quantity'))
                    ->skip(0)->take(5)->get();

        return response()->json($resultTop, 200);
    }

    public function getSupplierTop(){
        $date1 = new Carbon('2017-10-01');
        $date2 = new Carbon('2017-12-31');
        $resultSupplierTop = DB::table('contihogar_product')
                                ->leftJoin('contihogar_product_shop','contihogar_product_shop.id_product','=','contihogar_product.id_product')
                                ->leftJoin('contihogar_supplier','contihogar_supplier.id_supplier','=','contihogar_product.id_supplier')
                                ->leftJoin('contihogar_order_detail','contihogar_order_detail.product_id','=','contihogar_product.id_product')
                                ->leftJoin('contihogar_orders','contihogar_orders.id_order','=','contihogar_order_detail.id_order')
                            ->where('contihogar_orders.valid','=',$this->valid)
                            ->whereBetween(DB::raw('DATE(contihogar_orders.invoice_date)'),[$date1->toDateString(),$date2->toDateString()])
                            ->groupBy('contihogar_product.id_supplier')
                            ->groupBy('contihogar_supplier.name')
                            ->orderBy('quantity','DESC')
                            ->select('contihogar_product.id_supplier',
                                    'contihogar_supplier.name',
                                    DB::raw('SUM(contihogar_order_detail.product_quantity) as quantity'))
                            ->skip(0)->take(5)->get();
        
        return response()->json($resultSupplierTop, 200);
    }

    public function getCategoryTop(){
        $date1 = new Carbon('2017-10-01');
        $date2 = new Carbon('2017-12-31');

        $resultCategoryTop = DB::table('contihogar_category')
                                ->join('contihogar_category_lang','contihogar_category_lang.id_category','=','contihogar_category.id_category')
                                ->leftJoin('contihogar_category_product','contihogar_category_product.id_category','=','contihogar_category.id_category')
                                ->leftJoin('contihogar_product','contihogar_product.id_product','=','contihogar_category_product.id_product')
                                ->leftJoin('contihogar_order_detail','contihogar_order_detail.product_id','=','contihogar_product.id_product')
                                ->leftJoin('contihogar_orders','contihogar_orders.id_order','=','contihogar_order_detail.id_order')
                            ->where('contihogar_orders.valid','=',$this->valid)
                            ->whereBetween(DB::raw('DATE(contihogar_orders.invoice_date)'),[$date1->toDateString(),$date2->toDateString()])
                            ->groupBy('contihogar_category.id_category')
                            ->groupBy('contihogar_category_lang.name')
                            ->orderBy('quantity','DESC')
                            ->select('contihogar_category.id_category',
                                    'contihogar_category_lang.name',
                                    DB::raw('SUM(contihogar_order_detail.product_quantity) as quantity'))
                            ->skip(0)->take(5)->get();
        
        return response()->json($resultCategoryTop, 200);
    }

    public function getTotal(){
        $date1 = new Carbon('2017-10-01');
        $date2 = new Carbon('2017-12-31');

        $resultTotal = DB::table('contihogar_order_detail')
                        ->join('contihogar_orders','contihogar_orders.id_order','=','contihogar_order_detail.id_order')
                        ->where('contihogar_orders.valid','=',$this->valid)
                        ->whereBetween(DB::raw('DATE(contihogar_orders.invoice_date)'),[$date1->toDateString(),$date2->toDateString()])
                        ->select(
                            DB::raw('SUM(contihogar_orders.total_products_wt - contihogar_orders.total_discounts) total'),
                            DB::raw('SUM(contihogar_order_detail.product_quantity) as quantity'),
                            DB::raw('SUM(contihogar_order_detail.original_product_price - contihogar_order_detail.original_wholesale_price / contihogar_order_detail.original_product_price) as margen')
                        )->first();

        return response()->json($resultTotal, 200);
    }

    public function getGridOperation(Request $request){
        $listProduct = DB::table('contihogar_orders')
                        ->join('contihogar_order_detail','contihogar_order_detail.id_order','=','contihogar_orders.id_order')
                        ->join('contihogar_product','contihogar_product.id_product','=','contihogar_order_detail.product_id')
                    ->leftJoin('contihogar_supplier','contihogar_supplier.id_supplier','=','contihogar_product.id_supplier')
                    ->leftJoin('contihogar_customer','contihogar_customer.id_customer','=','contihogar_orders.id_customer')
                    ->leftJoin('contihogar_carrier','contihogar_carrier.id_carrier','=','contihogar_orders.id_carrier')
                    ->where('contihogar_orders.valid','=','1')
                    ->select(
                            'contihogar_order_detail.id_order_detail',
                            'contihogar_order_detail.id_order',
                            DB::raw('contihogar_order_detail.product_id as id_product'),
                            'contihogar_order_detail.product_attribute_id',
                            DB::raw('contihogar_order_detail.product_name as product'),
                            'contihogar_supplier.name as supplier',
                            'contihogar_order_detail.product_quantity as quantity',
                            'contihogar_order_detail.product_price',
                            'contihogar_order_detail.reduction_amount',
                            'contihogar_order_detail.reduction_amount_tax_excl',
                            'contihogar_order_detail.reduction_amount_tax_incl',
                            'contihogar_order_detail.reduction_percent',
                            'contihogar_order_detail.product_quantity_discount',
                            'contihogar_order_detail.total_price_tax_excl',
                            'contihogar_order_detail.total_price_tax_incl',
                            'contihogar_order_detail.total_shipping_price_tax_excl',
                            'contihogar_order_detail.total_shipping_price_tax_incl',
                            'contihogar_order_detail.purchase_supplier_price',
                            'contihogar_order_detail.original_product_price',
                            'contihogar_order_detail.original_wholesale_price',
                            'contihogar_orders.total_shipping',
                            'contihogar_orders.total_shipping_tax_incl',
                            'contihogar_orders.total_shipping_tax_excl',
                            DB::raw('0 cupon'),
                            'contihogar_orders.payment',
                            DB::raw('CASE contihogar_orders.payment 
                                WHEN "Culqi" THEN CONCAT("S/.",contihogar_order_detail.total_price_tax_incl * 0.08," - ", "8%")
                                WHEN "PagoEfectivo" THEN CONCAT("S/.",contihogar_order_detail.total_price_tax_incl * 0.05," - ", "5%")
                                ELSE "S/.0.00 - 0%"
                            END as "payment_letter"'),
                            'contihogar_orders.id_customer',
                            'contihogar_orders.id_carrier',
                            'contihogar_orders.id_address_delivery',
                            'contihogar_carrier.name as carrier',
                            DB::raw('concat(contihogar_carrier.name," - S/.", 0.00) as carrier_cost'),
                            DB::raw('"Cliente Cordinado" as "order_state"'),
                            DB::raw('"Pagado" as "contract_state"'),
                            DB::raw('0 as "reprogramation"'),
                            DB::raw('"" as comment'),
                            DB::raw('concat(contihogar_customer.firstname," ",contihogar_customer.lastname) as customer')
                    );
        $response["total"] = $listProduct->count();
        $response["data"] = $listProduct->get();

        return response()->json($response, 200);
    }

    public function changeProduct(Request $request){
        $eOrderDetail = $request;
        $mOrderDetail = OrderDetail::find($eOrderDetail["id_order_detail"]);
        $mOrderDetail->product_id = $eOrderDetail["id_product"];
        $mOrderDetail->product_attribute_id = $eOrderDetail["id_product_attribute"];
        $mOrderDetail->product_name = $eOrderDetail["product"];
        $mOrderDetail->save();

        return response()->json(array("response"=>true), 200);
        
    }

    public function calcShippingCost(Request $request){
        $oAddress = $request["address"];
        $oOrder = $request["order"];

        $listOrderDetail = OrderDetail::where('id_order','=',$oOrder["id_order"])->get();
        $shipping_cost = 0;
        $id_carrier1 = 0;
        $id_carrier2 = 0;
        foreach ($listOrderDetail as $key => $orderDetail) {
            $listProductItemShipping = DB::table('contihogar_product_item')
                                            ->join('contihogar_product_item_shipping','contihogar_product_item_shipping.id_product_item','=','contihogar_product_item.id_product_item')
                                            ->where('contihogar_product_item.id_product','=',$orderDetail->product_id)
                                            ->select(
                                                'contihogar_product_item_shipping.alto',
                                                'contihogar_product_item_shipping.ancho',
                                                'contihogar_product_item_shipping.profundidad',
                                                'contihogar_product_item_shipping.peso',
                                                'contihogar_product_item_shipping.cantidad',
                                                'contihogar_product_item_shipping.id_category_shipping'
                                            )->get();
            foreach ($listProductItemShipping as $key => $productItemShipping) {
                $shippingCostFormula = 0;
                $shippingCostCategory = 0;

                $altoAnchoProf = ((int)$productItemShipping->alto * (int)$productItemShipping->ancho * (int)$productItemShipping->profundidad / 6000);
                $altoAnchoProf = $altoAnchoProf > (int)$productItemShipping->peso?$altoAnchoProf:(int)$productItemShipping->peso;
                $sqlCarrierCost = DB::table('contihogar_carga_provincia')
                                    ->where('contihogar_carga_provincia.id_state','=',$oAddress["id_state"])
                                    ->where('contihogar_carga_provincia.id_provincia','=',$oAddress["id_provincia"])
                                    ->where('contihogar_carga_provincia.id_distrito','=',$oAddress["id_distrito"])
                                    ->select(
                                        'contihogar_carga_provincia.id_carrier',
                                        DB::raw('(contihogar_carga_provincia.kilo_base_final +('.floatval($altoAnchoProf).' - 1) * contihogar_carga_provincia.kilo_adicional * '.(int)$productItemShipping->cantidad.') as costo')
                                    )
                                    ->orderBy('costo','ASC')->first();
                if($sqlCarrierCost && $sqlCarrierCost != FALSE){
                    $shippingCostFormula += ($sqlCarrierCost->costo == NULL? 0:floatval($sqlCarrierCost->costo));
                    $id_carrier1 = ($sqlCarrierCost->id_carrier == NULL?3:(int)$sqlCarrierCost->id_carrier);
                }

                if($productItemShipping->id_category_shipping != 0 && $productItemShipping->id_category_shipping != 11){
                    $sqlCarrierCategory = DB::table('contihogar_carrier_category')
                                            ->where('contihogar_carrier_category.id_provincia','=',$oAddress["id_state"])
                                            ->where('contihogar_carrier_category.id_provincia','=',$oAddress["id_provincia"])
                                            ->where('contihogar_carrier_category.id_distrito','=',$oAddress["id_distrito"])
                                            ->where('contihogar_carrier_category.id_category','=',$productItemShipping->id_category_shipping)
                                            ->select(
                                                'contihogar_carrier_category.id_carrier',
                                                DB::raw('MIN(contihogar_carrier_category.costo) as costo')
                                            )->first();

                    if($sqlCarrierCategory){
                        $shippingCostCategory += ($sqlCarrierCategory->costo == NULL?0:floatval($sqlCarrierCategory->costo));
                        $id_carrier2 = $sqlCarrierCategory->id_carrier;
                    }

                    if($shippingCostFormula < $shippingCostCategory && $shippingCostCategory > 0){
                        $shipping_cost += $shippingCostFormula * $orderDetail->product_quantity;
                        $id_carrier2 = 0;
                    }else{
                        $shipping_cost += $shippingCostFormula * $orderDetail->product_quantity;
                        $id_carrier2 = 0;
                    }

                    if($shippingCostCategory < $shippingCostFormula && $shippingCostFormula > 0){
                        $shipping_cost += $shippingCostCategory * $orderDetail->product_quantity;
                        $id_carrier1 = 0;
                    }else{
                        $shipping_cost += $shippingCostCategory * $orderDetail->product_quantity;
                        $id_carrier1 = 0;
                    }
                }
            }
        }
        return response()->json(array("cost_shipping"=>($shipping_cost / $this->tax_payout_passarella),"id_carrier"=>($id_carrier1 == 0)?$id_carrier2:$id_carrier1),200);
    }
}

