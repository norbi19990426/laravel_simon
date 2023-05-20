<?php
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OrderController;

use Illuminate\Support\Facades\Route;

Route::get('/ingredientList', [IngredientController::class, 'index']);

Route::post('/ingredientCreate', [IngredientController::class, 'newIngredient']);

Route::post('/orderCreate', [OrderController::class, 'orderCreate']);

Route::get('/orderList', [OrderController::class, 'orderList']);

Route::delete('/orderDelete/{orderId}', [OrderController::class, 'orderDelete']);

Route::post('/orderUpdate/{orderId}', [OrderController::class, 'orderUpdate']);

Route::post('/orderBasedOnIngredient/{orderId}', [OrderController::class, 'orderBasedOnIngredient']);
