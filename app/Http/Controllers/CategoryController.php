<?php

namespace App\Http\Controllers;

use App\CategoryLang;
use Illuminate\Http\Request;
use App\Category;
use App\Library;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;


class CategoryController extends Controller
{
    private $id_lang=2;
    private $id_shop=1;
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

        $oCategoryLang= $eCategory['CategoryLang'];
        $mCategoryLang = new CategoryLang();
        $mCategoryLang->id_category =  $mCategory->id_category;
        $mCategoryLang->id_shop = $this->id_shop;
        $mCategoryLang->id_lang = $this->id_lang;
        $mCategoryLang->name = $oCategoryLang['name'];
        $mCategoryLang->description = $oCategoryLang['description'];
        $mCategoryLang->link_rewrite = $oCategoryLang['link_rewrite'];
        $mCategoryLang->meta_title = $oCategoryLang['meta_title'];
        $mCategoryLang->meta_keywords = $oCategoryLang['meta_keywords'];
        $mCategoryLang->meta_description = $oCategoryLang['meta_description'];
        $mCategoryLang->save();

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

//    {
//        $categoryByParents = DB::table('contihogar_category')
//            ->leftJoin('contihogar_category_lang', 'contihogar_category.id_category', '=','contihogar_category_lang.id_category')
//            ->leftJoin('contihogar_category_group', 'contihogar_category.id_category', '=', 'contihogar_category_group.id_category')
//            ->leftJoin('contihogar_category_shop', 'contihogar_category.id_category', '=', 'contihogar_category_shop.id_category')
//            ->where('contihogar_category_lang.id_lang', '=', $this->idlang)
//            ->where('contihogar_category.id_parent', '=', $id_parent)
//            ->groupBy('contihogar_category.id_category','')
//            ->orderBy('contihogar_category.id_category')
//            ->get();
//var_dump($categoryByParents);
//        $categoryByParents = DB::table('contihogar_category')
//            ->join('contihogar_category_lang', function ($join) {
//                $join->on('contihogar_category.id_category', '=', 'contihogar_category_lang.id_category');
//            })->where('contihogar_category_lang.id_lang', '=', $this->idlang)
//            ->where('contihogar_category.id_parent', '=', $id_parent)
//            ->get();

//        $facturasCliente = DB::table('contihogar_category')
//            ->leftJoin('contihogar_category_lang', 'contihogar_category.id_category', '=', 'contihogar_category_lang.id_category')
//            ->leftJoin('item_facturables', 'facturas.id', '=', 'item_facturables.id_factura')
//            ->select('clientes.*', 'facturas.id as id_factura', 'facturas.fecha', 'concepto')
//            ->where('clientes.email', '=', 'miguel@desarrolloweb.com')
//            ->get();
//        echo "<br>";


        $query = "SELECT    c.id_parent,
                            c.id_category,
                            CONCAT(REPLACE(REPLACE(REPLACE(REPLACE(level_depth,1,''),2,'--'),3,'---'),4,'----'),cl.name) as name,
                            cl.description,
                            cl.link_rewrite,
                            cs.position,
                            level_depth
                    FROM contihogar_category c
                    LEFT JOIN contihogar_category_lang cl ON (c.id_category = cl.id_category AND id_lang = '1')
                    LEFT JOIN contihogar_category_group cg ON (cg.id_category = c.id_category)
                    LEFT JOIN contihogar_category_shop cs ON (c.id_category = cs.id_category )
                    WHERE c.id_category <> :id_category
                    GROUP BY c.id_parent,c.id_category,cl.name,cl.description,cl.link_rewrite, cs.position, level_depth
                    ORDER BY cs.position ASC,c.id_parent ASC,level_depth ASC";

        $results = DB::select( DB::raw($query), array('id_category' => $id_category));
        //var_dump($results);

        return response()->json($results,200);
    }

    public function categoryByDepth()
    {

        $categoryByDepth = Category::with('CategoryLang')->where('level_depth','=','2')->orderBy('level_depth', 'asc')->get();

        return response()->json($categoryByDepth,200);
    }
}
