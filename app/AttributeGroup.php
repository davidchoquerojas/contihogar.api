<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AttributeGroup extends Model
{
    //
    protected $table = 'contihogar_attribute_group';
    protected $primaryKey = 'id_attribute_group';
    public $timestamps = false;
}
