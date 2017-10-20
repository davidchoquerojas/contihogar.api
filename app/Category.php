<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'contihogar_category';
    public function CategoryLang()
    {
        return $this->hasOne('App\CategoryLang','id_category','id_category');
    }
}
