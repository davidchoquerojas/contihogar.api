<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Manufacturer extends Model
{
    //
    protected $table = 'contihogar_manufacturer';
    protected $primaryKey = 'id_manufacturer';
    public $timestamps = false;
}
