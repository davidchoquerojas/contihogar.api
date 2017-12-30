<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;

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
}
