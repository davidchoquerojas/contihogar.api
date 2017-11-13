<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductAttribute extends Model
{
    //
    protected $table = 'contihogar_product_attribute';
    protected $primaryKey = 'id_product_attribute';
    public $timestamps = false;
}
