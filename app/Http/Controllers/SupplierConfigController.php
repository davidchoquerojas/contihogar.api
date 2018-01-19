<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierConfig;

class SupplierConfigController extends Controller
{
    //
    public function save($oSupplierConfig){
        $mSupplierConfig = new SupplierConfig();
        if(isset($oSupplierConfig["pay_before_delivery_product"]))
            $mSupplierConfig->pay_before_delivery_product = $oSupplierConfig["pay_before_delivery_product"];
        else
            $mSupplierConfig->pay_before_delivery_product = 0;
        if(isset($oSupplierConfig["previous_day"]))
            $mSupplierConfig->previous_day = $oSupplierConfig["previous_day"];
        else
            $mSupplierConfig->previous_day = 0;
        if(isset($oSupplierConfig["pay_after_delivery_product"]))
            $mSupplierConfig->pay_after_delivery_product = $oSupplierConfig["pay_after_delivery_product"];
        else
            $mSupplierConfig->pay_after_delivery_product = 0;
        if(isset($oSupplierConfig["percentage_pay_before_delivery"]))
            $mSupplierConfig->percentage_pay_before_delivery = $oSupplierConfig["percentage_pay_before_delivery"];
        else
            $mSupplierConfig->percentage_pay_before_delivery = 0;
        if(isset($oSupplierConfig["value_percentage_payout"]))
            $mSupplierConfig->value_percentage_payout = $oSupplierConfig["value_percentage_payout"];
        else
            $mSupplierConfig->value_percentage_payout = 0;
        if(isset($oSupplierConfig["pay_before_send_purchase_order"]))
            $mSupplierConfig->pay_before_send_purchase_order = $oSupplierConfig["pay_before_send_purchase_order"];
        else
            $mSupplierConfig->pay_before_send_purchase_order = 0;
        $mSupplierConfig->save();
    }

    public function update($oSupplierController,$id){
        $mSupplierConfig = SupplierConfig::find($id);
        $mSupplierConfig->pay_before_delivery_product = $oSupplierController["pay_before_delivery_product"];
        $mSupplierConfig->previous_day = $oSupplierController["previous_day"];
        $mSupplierConfig->pay_after_delivery_product = $oSupplierController["pay_after_delivery_product"];
        $mSupplierConfig->percentage_pay_before_delivery = $oSupplierController["percentage_pay_before_delivery"];
        $mSupplierConfig->value_percentage_payout = $oSupplierController["value_percentage_payout"];
        $mSupplierConfig->pay_before_send_purchase_order = $oSupplierController["pay_before_send_purchase_order"];
        $mSupplierConfig->save();

    }
}
