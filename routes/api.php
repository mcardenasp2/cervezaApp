<?php

use App\Http\Controllers\PulserasController;
use App\Http\Controllers\TransaccionController;
use App\Models\Pulsera;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/transacciones', [TransaccionController::class, 'store']);
Route::post('/verificar-asignacion', [PulserasController::class, 'checkAssignation']);
Route::post('/pulseras', [PulserasController::class, 'store']);

