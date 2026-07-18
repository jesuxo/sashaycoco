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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::group([ 'middleware' => 'check.user'], function () {

    Route::controller(\App\Http\Controllers\CwtokenController::class)->group(function () {
        Route::post('token/check/string', 'apiCheck');
        Route::post('token/new', 'newtoken');
    });

    Route::controller(\App\Http\Controllers\NewSaexisController::class)->group(function () {
        Route::post('newsaexis/sync/exist', 'newexistencias');
    });

    Route::controller(\App\Http\Controllers\SavendController::class)->group(function () {
        Route::post('savend/sync/list', 'list');
    });

    Route::controller(\App\Http\Controllers\CwtransferenciasController::class)->group(function () {
        Route::post('cwtransferencia/sync/list', 'list');
        Route::post('cwtransferencia/sync/apiStatus', 'apiStatus');
    });

    Route::controller(\App\Http\Controllers\CwcxcprvController::class)->group(function () {
        Route::post('cwcxcprv/sync/list', 'cuentasxpagar');
    });

    Route::controller(\App\Http\Controllers\SadepoController::class)->group(function () {
        Route::post('sadepo/sync/list', 'list');
    });

    Route::controller(\App\Http\Controllers\SainstaController::class)->group(function () {
        Route::post('sainsta/sync/list', 'list');
    });

    Route::controller(\App\Http\Controllers\SatarjController::class)->group(function () {
        Route::post('satarj/sync/list', 'list');
    });

    Route::controller(\App\Http\Controllers\SaclieController::class)->group(function () {
        Route::post('saclie/sync/list', 'list');
        Route::post('saclie/sync/sacliesucursal', 'sacliesucursal');
    });

    Route::controller(\App\Http\Controllers\SaprovController::class)->group(function () {
        Route::post('saprov/sync/list', 'list');
        Route::post('saprov/sync/saprovsucursal', 'saprovsucursal');
    });

    Route::controller(\App\Http\Controllers\SaservController::class)->group(function () {
        Route::post('saserv/sync/list', 'list');
        Route::post('saserv/sync/saservsucursal', 'saservsucursal');
    });


    Route::controller(\App\Http\Controllers\CwcuentasController::class)->group(function () {
        Route::post('cwcuentas/sync/list', 'list');
        Route::post('cwcuentas/sync/cwcuentasucursal', 'cwcuentasucursal');
    });

    Route::controller(\App\Http\Controllers\SaprodController::class)->group(function () {
        Route::post('saprod/sync/list', 'list');
        Route::post('saprod/get/listprodubic', 'listprodubic');
        Route::post('saprod/get/productosinstsancias', 'productosinstsancias');
        Route::post('saprod/get/productosinstsanciascodalte', 'productosinstsanciascodalte');
        Route::post('saprod/sync/saprodsucursal', 'saprodsucursal');
    });

    Route::controller(\App\Http\Controllers\SaexisController::class)->group(function () {
        Route::post('saexis/sync/exist', 'existencias');
    });

    Route::controller(\App\Http\Controllers\SafactController::class)->group(function () {
        Route::post('safact/sync/doc', 'documento');
        Route::post('safact/get/count', 'getcount');
    });

    Route::controller(\App\Http\Controllers\SacompController::class)->group(function () {
        Route::post('sacomp/sync/doc', 'documento');
    });

    Route::controller(\App\Http\Controllers\SaopeiController::class)->group(function () {
        Route::post('saopei/sync/doc', 'documento');
        Route::post('saopei/sync/descargar', 'descargar');
        Route::post('saopei/sync/descargado', 'descargado');
    });

    Route::controller(\App\Http\Controllers\SaacxcController::class)->group(function () {
        Route::post('saacxc/sync/cxc', 'cuentaxcobrar');
        Route::post('saacxc/sync/descargar' , 'descargar');
        Route::post('saacxc/sync/descargado', 'descargado');

        Route::post('saacxc/sync/descuento/descargar' , 'descargarDescuento');
        Route::post('saacxc/sync/descuento/descargado', 'descargadoDescuento');
    });

    Route::resource('saesta', \App\Http\Controllers\SaestaController::class);
    Route::resource('sucursal', \App\Http\Controllers\SasucursalController::class);

});
