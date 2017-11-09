<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Library;
use Illuminate\Support\Facades\DB;


class CategoryController extends Controller
{
    private $idlang=2;
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
        $mCategory->save();
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

    public function categoryByParents($id_parent)

    {
        $categoryByParents = DB::table('contihogar_category')
            ->join('contihogar_category_lang', function ($join) {
                $join->on('contihogar_category.id_category', '=', 'contihogar_category_lang.id_category');
            })->where('contihogar_category_lang.id_lang', '=', $this->idlang)
            ->where('contihogar_category.id_parent', '=', $id_parent)
            ->get();

        return response()->json($categoryByParents,200);
    }

    public function categoryByDepth()
    {

        $categoryByDepth = Category::with('CategoryLang')->where('level_depth','=','2')->orderBy('level_depth', 'asc')->get();

        return response()->json($categoryByDepth,200);
    }
}
