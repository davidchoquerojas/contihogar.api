<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\SupplierConfig;

class SupplierConfigController extends Controller
{
    //
    public function save($oSupplierController){
        $mSupplierConfig = new SupplierConfig();
        $mSupplierConfig->pay_before_delivery_product = $oSupplierController["pay_before_delivery_product"];
        $mSupplierConfig->previous_day = $oSupplierController["previous_day"];
        $mSupplierConfig->pay_after_delivery_product = $oSupplierController["pay_after_delivery_product"];
        $mSupplierConfig->percentage_pay_before_delivery = $oSupplierController["percentage_pay_before_delivery"];
        $mSupplierConfig->value_percentage_payout = $oSupplierController["value_percentage_payout"];
        $mSupplierConfig->pay_before_send_purchase_order = $oSupplierController["pay_before_send_purchase_order"];
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
