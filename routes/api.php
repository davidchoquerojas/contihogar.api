<?php

use Illuminate\Http\Request;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

/*Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('api')->get('/supplier', function (Request $request) {
     Route::resource('supplier','SupplierController@index');
});

Route::middleware('api')->post('/supplier/store', function (Request $request) {
    Route::resource('supplier','SupplierController@store');
});*/

Route::resource('address','AddressController');

Route::resource('carrier','CarrierController');

Route::resource('customer','CustomerController');

Route::resource('attribute', 'AttributeController');

Route::resource('productAttribute', 'ProductAttributeController');
Route::post('productAttribute/getByIdProduct','ProductAttributeController@getProductAttributeByIdProduct');
Route::post('productAttribute/store','ProductAttributeController@store');


Route::resource('productAttributeImage', 'ProductAttributeImageController');
Route::post('productAttributeImage/store', 'ProductAttributeImageController@store');



Route::post('supplier/store','SupplierController@store');
Route::resource('supplier','SupplierController');

//Route::get('manufacturer','ManufacturerController@index');
Route::resource("manufacturer",'ManufacturerController');

Route::resource('category', 'CategoryController');
Route::get('categoryByParents/{id}','CategoryController@categoryByParents');
Route::get('categoryByDepth', 'CategoryController@categoryByDepth');
Route::post('category/store','CategoryController@store');


Route::get('product/getProductGrid','ProductController@getProductGrid');
Route::post('product/store','ProductController@store');
/*Route::middleware('cors')->post('product/store', function (Request $request) {
    Route::post('product/store','ProductController@store');
});*/

Route::post('product/listarMerge','ProductController@mergeUpload');
Route::post('product/shippingCost','ProductController@getShippingCostById');
Route::post('product/getById','ProductController@getProductById');
Route::resource('product', 'ProductController');

Route::resource('productEvent', 'ProductEventController');

Route::resource('modelo', 'ModelController');

Route::resource('image', 'ImageController');
Route::post('image/store', 'ImageController@store');

Route::resource('departament', 'DepartamentController');
Route::get('province/{id_departament}', 'DepartamentController@getProvince');
Route::get('district/{id_province}', 'DepartamentController@getDistrict');
Route::get('zone/delivery','DepartamentController@getZoneDeliveryFree');

Route::get('order/productTop','OrderController@getProductTop');
Route::get('order/supplierTop','OrderController@getSupplierTop');
Route::get('order/categoryTop','OrderController@getCategoryTop');
Route::get('order/total','OrderController@getTotal');

Route::get('order/operation','OrderController@getGridOperation');
Route::post('order/changeProduct','OrderController@changeProduct');
Route::post('order/getCostShipping','OrderController@calcShippingCost');