<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class CargaProvincia extends Model
{
    //
    protected $table = 'contihogar_carga_provincia';
    protected $primaryKey = 'id_carga_provincia';
    public $timestamps = false;
}
