<?php

use App\Http\Controllers\ApiController;
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
Route::middleware('auth:api')->group(function () {
    Route::post('tables', [ApiController::class, 'index'])->name('api.tables');
    Route::post('get-table-info', [ApiController::class, 'getTableInfo'])->name('api.get-table-info');
    Route::post('delete-order', [ApiController::class, 'deleteOrder'])->name('api.delete-order');
    Route::post('create-order', [ApiController::class, 'createAndAssign'])->name('api.create-order');
    Route::post('save-comment', [ApiController::class, 'saveComment'])->name('api.save-comment');
    Route::post('pend-table', [ApiController::class, 'pend'])->name('api.pend-table');
    Route::post('deliver-order', [ApiController::class, 'deliver'])->name('api.deliver-table-orders');

    Route::post('logout', [ApiController::class, 'logout'])->name('api.logout');
});

Route::get('login', [ApiController::class, 'login'])->name('api.login');
