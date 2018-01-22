<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class OrderDetail extends Model
{
    //
    protected $table = 'contihogar_order_detail';
    protected $primaryKey = 'id_order_detail';
    public $timestamps = false;
}
