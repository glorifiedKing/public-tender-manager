<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

/*Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});*/

Route::group(['prefix' => 'user', 'as' => 'user.'],function(){
    Route::post('register','App\Http\Controllers\Api\UsersController@register')->name('register');
    Route::post('getToken','App\Http\Controllers\Api\UsersController@authenticate')->name('get.token');
    Route::post('logout','App\Http\Controllers\Api\UsersController@revoke_tokens')->name('logout');

});

Route::group(['prefix' => 'contracts','middleware'=>'auth:sanctum','as'=>'contracts.'],function(){
    Route::post('uploadContracts','App\Http\Controllers\Api\ContractsController@upload_contract')->name('upload.contracts');
    Route::post('getUploadStatus','App\Http\Controllers\Api\ContractsController@get_upload_progress')->name('upload.status');
    Route::get('getContract/{id}','App\Http\Controllers\Api\ContractsController@get_contract')->name('get.contract');
    Route::get('getContractReadStatus/{id}','App\Http\Controllers\Api\ContractsController@get_contract_read_status')->name('get.contract.read');
    Route::post('searchContracts','App\Http\Controllers\Api\ContractsController@search_contracts')->name('search.contracts');
});
