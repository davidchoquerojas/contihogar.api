<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    //
    protected $table = 'contihogar_image';
    protected $primaryKey = 'id_image';
    public  $timestamps = false;
}
