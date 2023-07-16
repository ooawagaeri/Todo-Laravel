<?php

use App\Http\Controllers\ItemController;
use App\Http\Controllers\SyncController;
use App\Http\Controllers\TodoListController;
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

Route::prefix('/todo-lists')->group(function() {
    Route::get('', [TodoListController::class, 'index']);
    Route::post('',[TodoListController::class, 'store']);
    Route::get('/{id}', [TodoListController::class, 'show']);
    Route::put('/{id}' , [TodoListController::class, 'update']);
    Route::delete('/{id}', [TodoListController::class, 'destroy'] );
});


Route::prefix('/todos')->group(function() {
    Route::get('', [ItemController::class, 'index']);
    Route::post('',[ItemController::class, 'store']);
    Route::get('/{id}', [ItemController::class, 'show']);
    Route::put('/{id}' , [ItemController::class, 'update']);
    Route::delete('/{id}', [ItemController::class, 'destroy'] );
});

Route::prefix('/sync')->group(function() {
    Route::put('',[SyncController::class, 'update']);
});