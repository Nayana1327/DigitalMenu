<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\PortionController;
use App\Http\Controllers\CuisineController;
use App\Http\Controllers\MenuController;
use App\Http\Controllers\WaiterController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\TableController;
use App\Http\Controllers\RiderController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/', function () {
    return Redirect::to('/login');
});

Auth::routes();
Route::group(['middleware' => ['web']] , function() {
Route::get('/home', [HomeController::class, 'index'])->name('home');

//Categories
Route::get('/category-list', [CategoryController::class, 'categoryList'])->name('category-list');
Route::get('/category-add', [CategoryController::class, 'categoryAddView'])->name('category-add');
Route::post('/category-add', [CategoryController::class, 'categoryAdd'])->name('category-insert');
Route::post('/category-delete', [CategoryController::class, 'categoryDelete'])->name('category-delete');

//Portions
Route::get('/portion-list', [PortionController::class, 'portionList'])->name('portion-list');
Route::get('/portion-add', [PortionController::class, 'portionAddView'])->name('portion-add');
Route::post('/portion-add', [PortionController::class, 'portionAdd'])->name('portion-insert');
Route::post('/portion-delete', [PortionController::class, 'portionDelete'])->name('portion-delete');

//Cuisines
Route::get('/cuisine-list', [CuisineController::class, 'cuisineList'])->name('cuisine-list');
Route::get('/cuisine-add', [CuisineController::class, 'cuisineAddView'])->name('cuisine-add');
Route::post('/cuisine-add', [CuisineController::class, 'cuisineAdd'])->name('cuisine-insert');
Route::post('/cuisine-delete', [CuisineController::class, 'cuisineDelete'])->name('cuisine-delete');

//Menus
Route::get('/menu-list', [MenuController::class, 'menuList'])->name('menu-list');
Route::get('/menu-add', [MenuController::class, 'menuAddView'])->name('menu-add');
Route::post('/menu-add', [MenuController::class, 'menuAdd'])->name('menu-insert');
Route::post('/menu-delete', [MenuController::class, 'menuDelete'])->name('menu-delete');
Route::post('/menu-unactivate', [MenuController::class, 'menuUnactivate'])->name('menu-unactivate');
Route::post('/menu-activate', [MenuController::class, 'menuActivate'])->name('menu-activate');
Route::get('/menu/{id}/edit', [MenuController::class, 'menuEdit'])->name('menu-edit');
Route::post('/menu/{id}/edit', [MenuController::class, 'menuUpdate'])->name('menu-update');

//Waiters
Route::get('/waiter-list', [WaiterController::class, 'waiterList'])->name('waiter-list');
Route::get('/waiter-add', [WaiterController::class, 'waiterAddView'])->name('waiter-add');
Route::post('/waiter-add', [WaiterController::class, 'waiterAdd'])->name('waiter-insert');
Route::post('/waiter-delete', [WaiterController::class, 'waiterDelete'])->name('waiter-delete');

//Delivery Persons
Route::get('/rider-list', [RiderController::class, 'riderList'])->name('rider-list');
Route::get('/rider-add', [RiderController::class, 'riderAddView'])->name('rider-add');
Route::post('/rider-add', [RiderController::class, 'riderAdd'])->name('rider-insert');
Route::post('/rider-delete', [RiderController::class, 'riderDelete'])->name('rider-delete');

//Tables
Route::get('/table-list', [TableController::class, 'tableList'])->name('table-list');
Route::get('/table-add', [TableController::class, 'tableAddView'])->name('table-add');
Route::post('/table-add', [TableController::class, 'tableAdd'])->name('table-insert');
Route::post('/table-delete', [TableController::class, 'tableDelete'])->name('table-delete');
Route::get('/table-availability', [TableController::class, 'tableAvailability'])->name('table-availability');
Route::get('/table-availability/{id}/edit', [TableController::class, 'tableAvailabilityEdit'])->name('tableAvailability-edit');
Route::post('/table-availability/{id}/edit', [TableController::class, 'tableAvailabilityUpdate'])->name('tableAvailability-update');

//Orders
Route::get('/order-list', [OrderController::class, 'orderList'])->name('order-list');
Route::get('/reports', [OrderController::class, 'reportView'])->name('reports');
Route::get('/export_report', [OrderController::class, 'exportReport'])->name('export_report');

});

Route::get('/clear', function() {

    Artisan::call('cache:clear');
    Artisan::call('config:cache');
    Artisan::call('view:clear');
    return "Cleared!";
});
