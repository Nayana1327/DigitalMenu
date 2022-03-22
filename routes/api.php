<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\Api\ApiController;
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
Route::post('/insert-order', [ApiController::class, 'insertOrders']);
Route::get('/get-order', [ApiController::class, 'getUserOrder']);


