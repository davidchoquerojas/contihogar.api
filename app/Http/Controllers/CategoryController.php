<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Category;
use App\Library;

class CategoryController extends Controller
{
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
        try{

            foreach($request->file('files') as $file){
                //print_r($file);
                $imginame = 'user_' . time() . '.' .$file->getClientOriginalExtension();
                //El public_path nos ubica en la carpeta public del proyecto
                $path = public_path() . '/img/users/';
                //La funcion move nos los guarda en la carpeta declarada en el path
                //print_r($file->getClientOriginalName());
                $file->move($path,$imginame);
                
                $url = 'http://www.contihogar.com.pe/api/images/products/11';
                $image_path = $path.$imginame;
                $key = 'SRT7462LNEFYYIVGARA1934VWCHW3ZGT';

                var_dump($image_path);
            
                $ch = curl_init();
                curl_setopt($ch, CURLOPT_VERBOSE, $url);
                curl_setopt($ch, CURLOPT_URL, $url);
                curl_setopt($ch, CURLOPT_POST, true);
                curl_setopt($ch, CURLOPT_USERPWD, $key.':');
                curl_setopt($ch, CURLOPT_POSTFIELDS, array('image' => '@'.$image_path.';type=image/jpg'));
                curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
                $result = curl_exec($ch);
                print_r($result);
                curl_close($ch);

               /* $key = 'SRT7462LNEFYYIVGARA1934VWCHW3ZGT';
                $url = 'http://www.contihogar.com.pe/api/images/products/11';
                $gblCall = curl_init();
                curl_setopt($gblCall, CURLOPT_URL, $url);
                curl_setopt($gblCall, CURLOPT_USERPWD, $key.':');
                curl_setopt($gblCall, CURLOPT_CUSTOMREQUEST, 'GET');
                //curl_setopt($gblCall, CURLOPT_GET, TRUE);
                curl_setopt($gblCall, CURLOPT_RETURNTRANSFER, TRUE);
                //curl_setopt($gblCall, CURLOPT_HTTPHEADER, $gblCallHeaders);
                curl_close($gblCall);

                $response = curl_exec($gblCall);*/
                //print_r($response);
                /*$debug = true;
                $ps_shop_path = "http://www.contihogar.com.pe";
                $ps_ws_auth_key = "SRT7462LNEFYYIVGARA1934VWCHW3ZGT";
                
                try
                {
                    $webService = new \App\Library\PrestaShopWebservice($ps_shop_path, $ps_ws_auth_key, $debug);
                    $xml = $webService->get(array('url' => $ps_shop_path.'/api/images/products/1?schema=blank'));
                    //$resources = $xml->children()->children();
                    var_dump($xml);
                    $image_path = $path.$imginame;
                    //$opt['postXml'] = $xml->asXML();
                    $opt['image'] = $image_path;
                    $xml = $webService->add($opt);
                    echo "Successfully added.";
                }
                catch (PrestaShopWebserviceException $e)
                {
                    // Here we are dealing with errors
                    $trace = $e->getTrace();
                    if ($trace[0]['args'][0] == 404) echo 'Bad ID';
                    else if ($trace[0]['args'][0] == 401) echo 'Bad auth key';
                    else echo 'Other error';
                }*/
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
}
