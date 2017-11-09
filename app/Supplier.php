<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    //
    protected $table = 'contihogar_supplier';
    protected $primaryKey = 'id_supplier';
    public $timestamps = false;
}