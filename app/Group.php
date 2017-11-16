<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    //
    protected $table = 'contihogar_group';
    protected $primaryKey = 'id_group';
    public $timestamps = false;
}
