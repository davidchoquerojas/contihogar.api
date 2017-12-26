<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Departament;
use  App\Province;
use  App\District;

class DepartamentController extends Controller
{
    private $id_country = 171;
    private $id_zone = 6;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
        $oDepartaments = Departament::where('id_country','=',$this->id_country)->get();
        /*$oListDepartament = array();
        foreach($oDepartaments as $oDepartament){
            array_push($oListDepartament,$oDepartament);
        }*/
        return response()->json($oDepartaments,200);
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
        //
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

    public function getProvince($id_departament){
        $oProvinces = Province::where('id_departamento','=',$id_departament)->get();
        /*$oListProvinces = array();
        foreach($oProvinces as $oProvince){
            array_push($oListProvinces,$oProvince);
        }*/
        return response()->json($oProvinces, 200);
    }

    public function getDistrict($id_province){
        $oDistrincts = District::where('id_provincia','=',$id_province)->get();
        /*$oListDistrinct = array();
        foreach($oDistrincts as $oDistrict){
            array_push($oListDistrinct,$oDistrict);
        }*/
        return response()->json($oDistrincts, 200);
    }

    public function getZoneDeliveryFree(){
        $oDepartaments = Departament::where('id_country','=',$this->id_country)->get();
        foreach($oDepartaments as $key=>$oDepartament){
            $oProvinces = Province::where('id_departamento','=',$oDepartament["id_state"])->get();
            foreach ($oProvinces as $key1 => $oProvince) {
                $oDistricts = District::where('id_provincia','=',$oProvince["id_provincia"])->get();
                $oProvinces[$key1]["Distrinct"] = $oDistricts;
            }
            $oDepartaments[$key]["Province"] = $oProvinces;
        }
        return response()->json($oDepartaments, 200);
    }
}
