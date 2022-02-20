<?php

use App\Http\Controllers\BillController;
use App\Http\Controllers\BalanceRecordController;

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

Route::post('/new-expense', [ BillController::class, 'newExpense'] );
Route::get('/user-expenses', [ BillController::class, 'listOfExpenses'] );
Route::get('/balances', [ BalanceRecordController::class, 'listOfBalances'] );
