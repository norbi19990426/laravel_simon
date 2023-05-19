<?php
use App\Http\Controllers\IngredientController;
use App\Http\Controllers\OrderController;

use Illuminate\Support\Facades\Route;

Route::get('/ingredientList', [IngredientController::class, 'index']);

Route::post('/ingredientCreate', [IngredientController::class, 'newIngredient']);

Route::post('/orderCreate', [OrderController::class, 'newOrder']);
