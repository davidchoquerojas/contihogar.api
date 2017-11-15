<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\ProductImage;
use App\Image;
use App\Library;

class ImageController extends Controller
{
    private $WebServiceUrl;
    private $WebServiceDebug;
    private $WebServiceKey;

    public function __construct(){
        $this->WebServiceUrl = \Config::get('constants.pressta_shop.url');
        $this->WebServiceDebug = \Config::get('constants.pressta_shop.debug');
        $this->WebServiceKey = \Config::get('constants.pressta_shop.key');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
            $id_product = $request->id_product;            
            foreach($request->file('files') as $key=>$file){
                $imginame = 'product_' . time() .'_'.$key. '.' .$file->getClientOriginalExtension();
                $path = public_path() . '/img/product';
                $file->move($path,$imginame);
                
                $image_path = $path.$imginame;
                $urlGrabar = $this->WebServiceUrl.'/api/images/products/'.$id_product;

                $image_path = "C:\Users\david\Documents\ContiHogar\contihogar.api\public/img/users/user_15097308770.png";
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_URL, $urlGrabar);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_USERPWD, $this->WebServiceKey.':');
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                //print_r($result);
                curl_close($ch);
            }
            return response()->json($request, 200);               
        }catch(Exception $e){
            return response()->json($e, 200);               
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
        try
        {
            //$webService = new \App\Library\PrestaShopWebservice($this->WebServiceUrl, $this->WebServiceKey, $this->WebServiceDebug);
            //$opt['resource'] = 'images/products/11';
            //$xml = 
            //$resources = $xml->children();
            //$webService->get($opt)

            $response = array("res"=>10);
            
            return response()->json($response, 200);
            
            //print_r($resources);
            /*foreach ($resources as $resource)
            {
                //*/
            //print_r("aqui");
        }
        catch (PrestaShopWebserviceException $e)
        {
            // Here we are dealing with errors
            $trace = $e->getTrace();
            if ($trace[0]['args'][0] == 404) echo 'Bad ID';
            else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
            else echo 'Other error';
        }
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
        try{
            $id_image = $id;
            ProductImage::where('id_image','=',$id_image)->delete();
            Image::destroy($id_image);

            return response()->json(array("res"=>true),200);
        }catch(Exception $ex){
            return response()->json($ex, 500);
        }
        
    }
}
