<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Carrier extends Model
{
    //
    protected $table = 'contihogar_carrier';
    protected $primaryKey = 'id_carrier';
    public $timestamps = false;
}
