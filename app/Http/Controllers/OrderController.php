<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\OrderDetail;

use DB;
use Carbon\Carbon;
class OrderController extends Controller
{
    private $valid = 1;
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
                            DB::raw('0 cupon'),
                            'contihogar_orders.payment',
                            DB::raw('CASE contihogar_orders.payment 
                                WHEN "Culqi" THEN CONCAT("S/.",contihogar_order_detail.total_price_tax_incl * 0.08," - ", "8%")
                                WHEN "PagoEfectivo" THEN CONCAT("S/.",contihogar_order_detail.total_price_tax_incl * 0.05," - ", "5%")
                                ELSE "S/.0.00 - 0%"
                            END as "payment_letter"'),
                            'contihogar_orders.id_customer',
                            'contihogar_orders.id_carrier',
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
}
