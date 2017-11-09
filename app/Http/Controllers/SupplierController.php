<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Supplier;
use Carbon\Carbon;


class SupplierController extends Controller
{
    private $id_shop = 0;
    private $id_lang = 2;
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $oResponse["oProduct"] = Supplier::all();
        return response()->json($oResponse,200);

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
        try{
            
            $oSupplier = $request;
            $mSupplier = new Supplier();
            $mSupplier->name = $oSupplier["Address"]["company"];
            $mSupplier->date_add = Carbon::now();
            $mSupplier->date_upd = Carbon::now();
            $mSupplier->active = $oSupplier["active"] == null?0:1;
            $mSupplier->save();
            return response()->json($mSupplier, 200);

            $oSupplierLang = $oSupplier["SupplierLang"];
            $mSupplierLang = new SupplierLang();
            $mSupplierLang->id_supplier = $mSupplier->id_supplier;
            $mSupplierLang->id_lang = $this->id_lang;
            $mSupplierLang->description = $oSupplierLang["description"];
            $mSupplierLang->meta_title = $oSupplierLang["meta_title"];
            $mSupplierLang->meta_keywords = $oSupplierLang["meta_keywords"];
            $mSupplierLang->meta_description = $oSupplierLang["meta_description"];
            $mSupplierLang->save();

            $mAddress = obtenerEntityAddress();
            $mAddress->save();

            $oSupplierMaufacturers = $oSupplier["SupplierMaufacturer"];
            foreach($oSupplierMaufacturers as $oSupplierMaufacturer){
                $id_manufacturer = $oSupplierMaufacturer["id_manufacturer"];
                if(intval($id_manufacturer) == 0){
                    $mManufacturer = new Manufacturer();
                    $mManufacturer->name = $oSupplierMaufacturer;
                    $mManufacturer->date_add = Carbon::now();
                    $mManufacturer->date_upd = Carbon::now();
                    $mManufacturer->active = 1;

                    $id_manufacturer = $mManufacturer->id_manufacturer;
                }
                $mSupplierManufacturer = new SupplierManufacturer();
                $mSupplierManufacturer->id_supplier = $mSupplier->id_supplier;;
                $mSupplierManufacturer->id_manufacturer = $id_manufacturer;
            }

            $mAddressAlmacen = obtenerEntityAddress();
            $mAddressAlmacen->save();

            $oSupplierContacts = $oSupplier["SupplierContact"];
            foreach($oSupplierContacts as $oSupplierContact){
                $mSupplierContact = new SupplierContact();
                $mSupplierContact->id_supplier = $mSupplier->id_supplier;
                $mSupplierContact->cargo = $oSupplierContact["cargo"];
                $mSupplierContact->nombres = $oSupplierContact["nombres"];
                $mSupplierContact->correo = $oSupplierContact["correo"];
                $mSupplierContact->telefono = $oSupplierContact["telefono"];
                $mSupplierContact->save();
            }

            $oSupplierZoneDeliverys = $oSupplier["SupplierZoneDelivery"];
            foreach($oSupplierZoneDelivery as $oSupplierZoneDelivery){
                $mSupplierZoneDelivery = new SupplierZoneDelivery();
                $mSupplierZoneDelivery->id_supplier = $mSupplier->id_supplier;
                $mSupplierZoneDelivery->id_state = $oSupplierZoneDelivery["id_state"];
                $mSupplierZoneDelivery->id_provincia = $oSupplierZoneDelivery["id_provincia"];
                $mSupplierZoneDelivery->id_distrito = $oSupplierZoneDelivery["id_distrito"];
                $mSupplierZoneDelivery->save();
            }


        }catch(Exeptions $e){            
            return response()->json($e, 500);
        }
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
        $id_supplier = $id;
        $oSupplier = Supplier::find($id_supplier);
        $oSupplier["SupplierLang"] = SupplierLang::get()->where('id_supplier','=',$oSupplier->id_supplier);
        $oSupplier["SupplierManufacturer"] = SupplierManufacturer::get()->where('id_supplier','=',$oSupplier->id_supplier);
        foreach($oSupplier["SupplierManufacturer"] as $oSupplierManufacturer){
            $oSupplier["SupplierManufacturer"]["Manufacturer"] = Manufacturer::find($oSupplierManufacturer->id_manufacturer)->first();
        }
        $oSupplier["SupplierZoneDelevery"] = SupplierZoneDelevery::get()->where('id_supplier','=',$oSupplier->id_supplier);
        $oSupplier["Address"] = Address::get()->where('id_supplier','=',$oSupplier->id_supplier);

        return response()->json($oSupplier, 200);
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
    public function obtenerEntityAddress($oAddressEntity){
        $mAdress = new Address();
        $mAdress->id_country = $oAddressEntity["id_country"];
        $mAdress->id_state = $oAddressEntity["id_state"];
        $mAdress->id_distrito = $oAddressEntity["id_distrito"];
        $mAdress->id_provincia = $oAddressEntity["id_provincia"];
        $mAdress->id_customer = $oAddressEntity["id_customer"];
        $mAdress->id_manufacturer = $oAddressEntity["id_manufacturer"];
        $mAdress->id_supplier = $oAddressEntity["id_supplier"];
        $mAdress->id_warehouse = $oAddressEntity["id_warehouse"];
        $mAdress->alias = $oAddressEntity["alias"];
        $mAdress->company = $oAddressEntity["company"];
        $mAdress->lastname = $oAddressEntity["lastname"];
        $mAdress->firstname = $oAddressEntity["firstname"];
        $mAdress->address1 = $oAddressEntity["address1"];
        $mAdress->address2 = $oAddressEntity["address2"];
        $mAdress->postcode = $oAddressEntity["postcode"];
        $mAdress->city = $oAddressEntity["city"];
        $mAdress->other = $oAddressEntity["other"];
        $mAdress->phone = $oAddressEntity["phone"];
        $mAdress->phone_mobile = $oAddressEntity["phone_mobile"];
        $mAdress->vat_number = $oAddressEntity["vat_number"];
        $mAdress->dni = $oAddressEntity["dni"];
        $mAdress->date_add = $oAddressEntity["date_add"];
        $mAdress->date_upd = $oAddressEntity["date_upd"];
        $mAdress->active = $oAddressEntity["active"];
        $mAdress->deleted = $oAddressEntity["deleted"];
        return $mAdress;
    }
}
