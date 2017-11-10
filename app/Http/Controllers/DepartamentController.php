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
        $oDepartaments = Departament::get()->where('id_country','=',$this->id_country);
        $oListDepartament = array();
        foreach($oDepartaments as $oDepartament){
            array_push($oListDepartament,$oDepartament);
        }
        return response()->json($oListDepartament,200);
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
        $oProvinces = Province::get()->where('id_departamento','=',$id_departament);
        $oListProvinces = array();
        foreach($oProvinces as $oProvince){
            array_push($oListProvinces,$oProvince);
        }
        return response()->json($oListProvinces, 200);
    }

    public function getDistrict($id_province){
        $oDistrincts = District::get()->where('id_provincia','=',$id_province);
        $oListDistrinct = array();
        foreach($oDistrincts as $oDistrict){
            array_push($oListDistrinct,$oDistrict);
        }
        return response()->json($oListDistrinct, 200);
    }

    public function getZoneDeliveryFree(){
        $oDepartaments = Departament::get()->where('id_country','=',$this->id_country);
        $oListDepartament = array();
        foreach($oDepartaments as $key=>$oDepartament){
            $oProvinces = Province::get()->where('id_departamento','=',$oDepartament["id_state"]);
            $oListProvinces = array();
            foreach ($oProvinces as $key1 => $oProvince) {
                $oDistricts = District::get()->where('id_provincia','=',$oProvince["id_provincia"]);
                $oListDistrinct = array();
                foreach($oDistricts as $oDistrict){
                    array_push($oListDistrinct,$oDistrict);
                }
                $oProvinces[$key1]["Distrinct"] = $oListDistrinct;
                array_push($oListProvinces,$oProvinces[$key1]);
            }
            $oDepartaments[$key]["Province"] = $oListProvinces;
            array_push($oListDepartament,$oDepartaments[$key]);
        }
        return response()->json($oListDepartament, 200);
    }
}
