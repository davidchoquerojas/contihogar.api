<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SpecificPrice extends Model
{
    //
    protected $table = 'contihogar_specific_price';
    protected $primaryKey = 'id_specific_price';
    public $timestamps = false;
}
