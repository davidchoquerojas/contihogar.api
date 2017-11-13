<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Attribute extends Model
{
    //
    protected $table = 'contihogar_attribute';
    protected $primaryKey = 'id_attribute';
    public $timestamps = false;

}
