<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class District extends Model
{
    //
    protected $table = 'contihogar_distrito';
    protected $primaryKey = 'id_distrito';
    public  $timestamps = false;
}
