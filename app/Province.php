<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Province extends Model
{
    //
    protected $table = 'contihogar_provincia';
    protected $primaryKey = 'id_provincia';
    public  $timestamps = false;
}
