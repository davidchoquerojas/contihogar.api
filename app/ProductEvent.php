<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ProductEvent extends Model
{
    //
    protected $table = 'contihogar_product_event';
    protected $primaryKey = 'id_product_event';
    public $timestamps = false;
}
