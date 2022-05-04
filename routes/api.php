<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiController;
use App\Http\Middleware\verifyApiToken;
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

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::get('/category-list', [ApiController::class, 'listCategories']);
Route::get('/menu-list', [ApiController::class, 'listMenus']);
Route::get('/table-list', [ApiController::class, 'listTables']);
Route::post('/select-table', [ApiController::class, 'selectTable']);
Route::post('/search-menu', [ApiController::class, 'searchByMenu']);
Route::post('/insert-order', [ApiController::class, 'insertOrder']);
Route::get('/get-order', [ApiController::class, 'getOrder']);
Route::post('/update-order', [ApiController::class, 'updateOrder']);
Route::post('/delete-order', [ApiController::class, 'deleteOrder']);
Route::get('/table-order', [ApiController::class, 'getTableOrder']);
Route::post('/device-token', [ApiController::class, 'deviceToken']);

Route::post('/sendNotification', [ApiController::class, 'sendNotification']);
Route::post('/waiter-login', [ApiController::class, 'waiterLogin']);


Route::middleware([verifyApiToken::class])->group(function () {
    Route::get('/test', [ApiController::class, 'test']);
    Route::post('/order-completion', [ApiController::class, 'orderCompletion']);
    Route::post('/delete-menu', [ApiController::class, 'deleteMenuItems']);
});
