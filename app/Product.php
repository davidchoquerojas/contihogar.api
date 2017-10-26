<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    //
    protected $table = 'contihogar_product';
    protected $primaryKey = 'id_product';
    public $timestamps = false;
    public function ProductLang()
    {
        return $this->hasOne('App\ProductLang','id_product','id_product');
    }
}
