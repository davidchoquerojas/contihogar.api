<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    //
    protected $table = 'contihogar_address';
    protected $primaryKey = 'id_address';
    public $timestamps = false;
}
