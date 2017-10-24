<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductItem extends Model
{
    //
    protected $table = 'contihogar_product_item';
    protected $primaryKey = 'id_product_item';
    public $timestamps = false;
}
