<?php

namespace App\Http\Controllers;
use App\Models\Ingredient;

use Illuminate\Http\Request;

class IngredientController extends Controller
{
    public function index(){
        $ingredient = Ingredient::all();
        return $ingredient;
    }
    public function newIngredient(Request $request){
        $newIngredient = $request->all();

        $ingredient = new Ingredient;
        $ingredient->ingredient_name = $newIngredient["ingredient_name"];
        $ingredient->ingredient_price = $newIngredient["ingredient_price"];
        $ingredient->save();

        $ingredientAll = Ingredient::all();
        return $ingredientAll;
    }
}
