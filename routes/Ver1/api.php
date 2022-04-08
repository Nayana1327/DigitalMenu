<?php

use Illuminate\Support\Facades\Route;

use App\Http\Middleware\verifyApiToken;

use App\Http\Controllers\Api\V1\MenuController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\TableController;
use App\Http\Controllers\Api\V1\WaiterController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\CategoryController;
/*
|--------------------------------------------------------------------------
| Version 1 API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register Version 1 API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::get('/category-list', [CategoryController::class, 'list']);

Route::get('/menu-list', [MenuController::class, 'list']);
Route::post('/search-menu', [MenuController::class, 'search']);

Route::get('/table-list', [TableController::class, 'list']);

Route::post('/insert-order', [OrderController::class, 'store']);
Route::get('/get-order', [OrderController::class, 'getOrder']);

Route::post('/waiter-login', [WaiterController::class, 'login']);

Route::post('/device-token', [DeviceController::class, 'store']);
// Route::get('/truncate', [DeviceController::class, 'truncate']);

Route::middleware([verifyApiToken::class])->group(function () {
    Route::post('/delete-menu', [OrderController::class, 'deleteMenuOrder']);
    Route::get('/order-completion', [OrderController::class, 'orderCompletion']);
});
