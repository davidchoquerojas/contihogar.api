<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductShop extends Model
{
    //
    protected $table = 'contihogar_product_shop';
    protected $primaryKey = 'id_product';
    public $incrementing = false;
    public $timestamps = false;
}
