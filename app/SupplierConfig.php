<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SupplierConfig extends Model
{
    //
    protected $table = 'contihogar_supplier_config';
    protected $primaryKey = 'id_supplier_config';
    public $timestamps = false;
}
