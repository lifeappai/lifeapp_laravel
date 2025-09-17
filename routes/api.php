<?php

use Illuminate\Support\Facades\Route;
Use App\Http\Controllers\Api\Web\AdminController;
use App\Http\Controllers\AppVersionController;

/*
|--------------------------------------------------------------------------
| Web API Routes
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

/*
 * --------------------------------------------------------------------------
 * Admin Routes
 * --------------------------------------------------------------------------
 */
Route::prefix('admin')->group(function () {
    Route::post('login',[AdminController::class,'login']);
    Route::post('register',[AdminController::class,'store']);
});

Route::get('app-version', [AppVersionController::class, 'getLatestVersion']);
