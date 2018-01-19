<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    //
    protected $table = 'contihogar_category';
    protected $primaryKey = 'id_category';
    public  $timestamps = false;

    public function CategoryLang()
    {
        return $this->hasOne('App\CategoryLang','id_category','id_category')->where('id_lang', '=', 2);;
    }
}
