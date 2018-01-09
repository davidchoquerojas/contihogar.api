<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\CategoryLang;
use App\CategoryShop;
use App\Category;
use App\Group;
use App\CategoryGroup;

use App\Library;

use Carbon\Carbon;


class CategoryController extends Controller
{
    private $id_lang = 2;
    private $id_shop = 1;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $oCategory = Category::with('CategoryLang')->where('level_depth','>','0')->orderBy('level_depth', 'asc')->get();

        return response()->json($oCategory,200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $eCategory =  $request;
        $mCategory = new Category();
        $mCategory->id_parent = $eCategory["id_parent"];
        $mCategory->id_shop_default = 1;
        $mCategory->level_depth = $eCategory["level_depth"];
        $mCategory->nleft = 0;
        $mCategory->nright = 0;
        $mCategory->active = $eCategory["active"];
        $mCategory->date_add = Carbon::now();
        $mCategory->date_upd = Carbon::now();
        $mCategory->save();

        $oCategoryLang = $eCategory['CategoryLang'];
        $this->grabarCategoryLang($oCategoryLang,$mCategory->id_category,true);

        $mCategoryShop = new CategoryShop();
        $mCategoryShop->id_category = $mCategory->id_category;
        $mCategoryShop->id_shop = $this->id_shop;
        $mCategoryShop->position++;
        $mCategoryShop->save();

        $mGroups = Group::all();
        foreach ($mGroups as $key => $oGroup) {
            $mCategoryGroup = new CategoryGroup();
            $mCategoryGroup->id_category = $mCategory->id_category;
            $mCategoryGroup->id_group = $oGroup->id_group;
            $mCategoryGroup->save();
        }

        return response()->json($mCategory,200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
        $id_category = $id;
        $oCategory = Category::find($id_category);
        $oCategory["CategoryLang"] = CategoryLang::where('id_category','=',$oCategory["id_category"])->where('id_lang','=',$this->id_lang)->first();
        return response()->json($oCategory, 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
        $id_category = $id;
        $oCategory = $request;
        $mCategory = Category::find($id_category);
        if($oCategory["isUpdateAll"] == FALSE){
            $mCategory->active = $oCategory["active"];
            $mCategory->save();
        }else{
            $mCategory->date_upd = Carbon::now();
            $mCategory->save();

            $oCategoryLang = $oCategory["CategoryLang"]; 
            $this->grabarCategoryLang($oCategoryLang,$id_category,false);

        }

        return response()->json(array("res"=>true), 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function categoryByParents($id_category)
    {
        $query = "SELECT    c.id_parent,
                            c.id_category,
                            CONCAT(REPLACE(REPLACE(REPLACE(REPLACE(level_depth,1,''),2,'-- '),3,'--- '),4,'---- '),cl.name) as name,
                            cs.position,
                            level_depth,
                            c.active,
                            cl.link_rewrite
                    FROM contihogar_category c
                    LEFT JOIN contihogar_category_lang cl ON (c.id_category = cl.id_category AND id_lang = :id_lang)
                    LEFT JOIN contihogar_category_group cg ON (cg.id_category = c.id_category)
                    LEFT JOIN contihogar_category_shop cs ON (c.id_category = cs.id_category )
                    WHERE c.id_category <> :id_category
                    GROUP BY c.id_parent,c.id_category,cl.name, cs.position, level_depth,c.active,cl.link_rewrite
                    ORDER BY cs.position ASC,c.id_parent ASC,level_depth ASC";

        $results = DB::select(DB::raw($query), array('id_lang'=>$this->id_lang,'id_category' => $id_category));
        //var_dump($results);

        return response()->json($results,200);
    }

    public function categoryByDepth()
    {
        $categoryByDepth = Category::with('CategoryLang')->where('level_depth','=','2')->orderBy('level_depth', 'asc')->get();
        return response()->json($categoryByDepth,200);
    }

    /**
     * Graba la entidad especificada CategoryLang
     * 
     * @param \App\CategoryLang
     * @return void
     */
    private function grabarCategoryLang($oCategoryLang,$id_category,$isNew){
        if(!$isNew){
            CategoryLang::where('id_category','=',$id_category)->where('id_lang','=',$this->id_lang)->delete();
        }
        $mCategoryLang = new CategoryLang();
        $mCategoryLang->id_category = $id_category;
        $mCategoryLang->id_shop = $this->id_shop;
        $mCategoryLang->id_lang = $this->id_lang;
        $mCategoryLang->name = $oCategoryLang['name'];
        $mCategoryLang->description = $oCategoryLang['description'];
        $mCategoryLang->link_rewrite = $oCategoryLang['link_rewrite'];
        $mCategoryLang->meta_title = $oCategoryLang['meta_title'];
        $mCategoryLang->meta_keywords = $oCategoryLang['meta_keywords'];
        $mCategoryLang->meta_description = $oCategoryLang['meta_description'];
        $mCategoryLang->save();
    }
}
