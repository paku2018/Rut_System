<?php

use Illuminate\Support\Facades\Route;

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

Route::get('/', function () {
    return redirect('login');
});

Route::get('/jspm', [App\Http\Controllers\JSPMController::class, 'index'])->name('lincencia_jspm');
Route::get('/jspmtest', [App\Http\Controllers\JSPMController::class, 'test'])->name('lincencia_jspm_test');

Auth::routes();
Route::get('/verify', [App\Http\Controllers\HomeController::class, 'verify'])->name('verify');
Route::post('/verify/check_code', [App\Http\Controllers\HomeController::class, 'checkCode'])->name('check-verify-code');
Route::post('/verify/resend_code', [App\Http\Controllers\HomeController::class, 'resendCode'])->name('resend-code');

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
Route::get('/restaurant-menu/{code}', [App\Http\Controllers\HomeController::class, 'menu'])->name('restaurant-menu');
Route::post('/send-mail', [App\Http\Controllers\HomeController::class, 'sendVerificationMail'])->name('send-verification-mail');
Route::post('/order', [App\Http\Controllers\HomeController::class, 'order'])->name('order');
Route::get('/order-detail/{tableId}', [App\Http\Controllers\HomeController::class, 'orderDetail'])->name('order-detail');


Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/profile', [App\Http\Controllers\HomeController::class, 'profile'])->name('profile');
    Route::post('/profile/update', [App\Http\Controllers\HomeController::class, 'updateProfile'])->name('profile.update');
    Route::post('/getTableInfo', [App\Http\Controllers\Member\TableController::class, 'getTableInfo'])->name('get-table-info');
    Route::get('/exportPdf/{id}', [App\Http\Controllers\HomeController::class, 'exportPdf'])->name('export-pdf');
    Route::post('/createOrder', [App\Http\Controllers\Member\OrderController::class, 'createAndAssign'])->name('create-order');
    Route::post('/deleteOrder', [App\Http\Controllers\Member\TableController::class, 'deleteOrder'])->name('delete-order');
    Route::post('/deliverOrders', [App\Http\Controllers\Member\TableController::class, 'deliver'])->name('deliver-table-orders');
});

Route::group(['as' =>'admin.','prefix'=>'admin','middleware'=>'checkAdmin'],function () {
    Route::get('/home', [App\Http\Controllers\Admin\HomeController::class, 'index'])->name('home');

    Route::group(['prefix' => 'restaurant', 'as' => 'restaurant.'], function () {
        Route::get('/list', [App\Http\Controllers\Admin\RestaurantController::class, 'index'])->name('list');
        Route::get('/create', [App\Http\Controllers\Admin\RestaurantController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\RestaurantController::class, 'edit'])->name('edit');
        Route::post('/store', [App\Http\Controllers\Admin\RestaurantController::class, 'store'])->name('store');
        Route::post('/delete', [App\Http\Controllers\Admin\RestaurantController::class, 'delete'])->name('delete');
    });

    Route::group(['prefix' => 'user', 'as' => 'user.'], function () {
        Route::get('/list', [App\Http\Controllers\Admin\UserController::class, 'index'])->name('list');
        Route::get('/create', [App\Http\Controllers\Admin\UserController::class, 'create'])->name('create');
        Route::get('/edit/{id}', [App\Http\Controllers\Admin\UserController::class, 'edit'])->name('edit');
        Route::post('/store', [App\Http\Controllers\Admin\UserController::class, 'store'])->name('store');
        Route::post('/delete', [App\Http\Controllers\Admin\UserController::class, 'delete'])->name('delete');
    });
});

Route::group(['middleware'=>'checkResAdmin'],function () {
    Route::get('/dashboard', [App\Http\Controllers\ResAdmin\HomeController::class, 'index'])->name('restaurant.home');

    Route::group(['prefix' => 'restaurant', 'as' => 'restaurant.'], function () {
        Route::get('/list', [App\Http\Controllers\ResAdmin\RestaurantController::class, 'index'])->name('list');
        Route::get('/detail/{id}', [App\Http\Controllers\ResAdmin\RestaurantController::class, 'detail'])->name('detail');
        Route::get('/tables', [App\Http\Controllers\ResAdmin\TableController::class, 'index'])->name('tables.list');
        Route::get('/tables/create', [App\Http\Controllers\ResAdmin\TableController::class, 'create'])->name('tables.create');
        Route::get('/tables/create-delivery', [App\Http\Controllers\ResAdmin\TableController::class, 'createDelivery'])->name('tables.create-delivery');
        Route::post('/tables/store-delivery', [App\Http\Controllers\ResAdmin\TableController::class, 'storeDelivery'])->name('tables.store-delivery');
        Route::get('/tables/edit/{id}', [App\Http\Controllers\ResAdmin\TableController::class, 'edit'])->name('tables.edit');
        Route::post('/tables/store', [App\Http\Controllers\ResAdmin\TableController::class, 'store'])->name('tables.store');
        Route::post('/tables/delete', [App\Http\Controllers\ResAdmin\TableController::class, 'delete'])->name('tables.delete');
        Route::post('/tables/list', [App\Http\Controllers\ResAdmin\TableController::class, 'getList'])->name('tables.get-list');

        Route::get('/members', [App\Http\Controllers\ResAdmin\UserController::class, 'index'])->name('members.list');
        Route::get('/members/create', [App\Http\Controllers\ResAdmin\UserController::class, 'create'])->name('members.create');
        Route::get('/members/edit/{id}', [App\Http\Controllers\ResAdmin\UserController::class, 'edit'])->name('members.edit');
        Route::post('/members/store', [App\Http\Controllers\ResAdmin\UserController::class, 'store'])->name('members.store');
        Route::post('/members/delete', [App\Http\Controllers\ResAdmin\UserController::class, 'delete'])->name('members.delete');

        Route::get('/categories', [App\Http\Controllers\ResAdmin\CategoryController::class, 'index'])->name('categories.list');
        Route::get('/categories/create', [App\Http\Controllers\ResAdmin\CategoryController::class, 'create'])->name('categories.create');
        Route::get('/categories/edit/{id}', [App\Http\Controllers\ResAdmin\CategoryController::class, 'edit'])->name('categories.edit');
        Route::post('/categories/store', [App\Http\Controllers\ResAdmin\CategoryController::class, 'store'])->name('categories.store');
        Route::post('/categories/delete', [App\Http\Controllers\ResAdmin\CategoryController::class, 'delete'])->name('categories.delete');

        Route::get('/products', [App\Http\Controllers\ResAdmin\ProductController::class, 'index'])->name('products.list');
        Route::get('/products/create', [App\Http\Controllers\ResAdmin\ProductController::class, 'create'])->name('products.create');
        Route::get('/products/edit/{id}', [App\Http\Controllers\ResAdmin\ProductController::class, 'edit'])->name('products.edit');
        Route::post('/products/store', [App\Http\Controllers\ResAdmin\ProductController::class, 'store'])->name('products.store');
        Route::post('/products/delete', [App\Http\Controllers\ResAdmin\ProductController::class, 'delete'])->name('products.delete');
        Route::post('/products/change', [App\Http\Controllers\ResAdmin\ProductController::class, 'change'])->name('products.change');

        Route::get('/qrcode', [App\Http\Controllers\ResAdmin\HomeController::class, 'qrcode'])->name('qrcode');

        Route::post('/closeTable', [App\Http\Controllers\ResAdmin\TableController::class, 'close'])->name('close-table');

        Route::group(['prefix' => 'statistics', 'as' => 'statistics.'], function () {
            Route::get('/sales', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'salesIndex'])->name('sales');
            Route::post('/sales/getData', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'getSalesData'])->name('sales.get-data');
            Route::post('/sales/export', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'salesExport'])->name('sales.export');

            Route::get('/orders', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'ordersIndex'])->name('orders');

            Route::get('/bestProducts', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'bestProductIndex'])->name('best-products');
            Route::post('/bestProducts/getData', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'bestProductData'])->name('best-products.get-data');
            Route::post('/bestProducts/export', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'bestProductExport'])->name('best-products.export');

            Route::get('/breakdown', [App\Http\Controllers\ResAdmin\StatisticsController::class, 'breakdownIndex'])->name('breakdown');
        });

        Route::group(['prefix' => 'sales', 'as' => 'sales.'], function () {
            Route::get('/index', [App\Http\Controllers\ResAdmin\SalesController::class, 'index'])->name('index');
            Route::post('/get-products', [App\Http\Controllers\ResAdmin\SalesController::class, 'getProducts'])->name('get-products');
        });
    });
});

Route::group(['middleware'=>'checkMember'],function () {
    Route::post('/getOrders', [App\Http\Controllers\Member\OrderController::class, 'getData'])->name('get-order-data');
    Route::post('/assignOrder', [App\Http\Controllers\Member\OrderController::class, 'assign'])->name('assign-orders');

    Route::group(['prefix' => 'waiter', 'as' => 'waiter.'], function () {
        Route::get('/tables', [App\Http\Controllers\Member\TableController::class, 'index'])->name('tables');
        Route::post('/get-table-list', [App\Http\Controllers\Member\TableController::class, 'getList'])->name('get-table-list');
        Route::post('/pendTable', [App\Http\Controllers\Member\TableController::class, 'pend'])->name('pend-table');
    });
});
