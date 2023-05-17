<?php
use App\Http\Controllers\IngredientController;

use Illuminate\Support\Facades\Route;

Route::get('/', [IngredientController::class, 'index']);
