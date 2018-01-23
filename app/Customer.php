<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    //
    protected $table = 'contihogar_customer';
    protected $primaryKey = 'id_customer';
    public $timestamps = false;
}
