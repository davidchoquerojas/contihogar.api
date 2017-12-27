<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class StockAvailable extends Model
{
    //
    protected $table = 'contihogar_stock_available';
    protected $primaryKey = 'id_stock_available';
    public  $timestamps = false;
}
