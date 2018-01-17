<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class LayeredPriceIndex extends Model
{
    //
    protected $table = 'contihogar_layered_price_index';
    protected $primaryKey = 'id_product';
    public $incrementing = false;
    public $timestamps = false;
}
