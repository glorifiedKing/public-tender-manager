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
    Route::post('register','App\Http\Controllers\Api\UsersController@register');
    Route::post('getToken','App\Http\Controllers\Api\UsersController@authenticate');
    Route::post('logout','App\Http\Controllers\Api\UsersController@revoke_tokens');

});

Route::group(['prefix' => 'contracts','middleware'=>'auth:sanctum','as'=>'contracts.'],function(){
    Route::post('uploadContracts','App\Http\Controllers\Api\ContractsController@upload_contract');
    Route::post('getUploadStatus','App\Http\Controllers\Api\ContractsController@get_upload_progress');
    Route::get('getContract/{id}','App\Http\Controllers\Api\ContractsController@get_contract');
    Route::get('getContractReadStatus/{id}','App\Http\Controllers\Api\ContractsController@get_contract_read_status');
    Route::post('searchContracts','App\Http\Controllers\Api\ContractsController@search_contracts');
});
