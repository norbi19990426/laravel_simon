<?php

namespace App\Http\Controllers;
use App\Models\Ingredient;
use App\Models\OrderData;
use App\Models\Order;

use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function newOrder(Request $request){
        $newOrder = $request->all();
        $ingredients = Ingredient::all();
        $ingredientIsExists = [];

        foreach ($newOrder as $orderValue) {
            foreach ($ingredients as $ingredient) {
                if($ingredient["ingredient_name"] == $orderValue["name"]){
                   array_push($ingredientIsExists, 
                   [
                    "ingredient_id" => $ingredient["ingredient_id"],
                    "ingredient_name" => $ingredient["ingredient_name"],
                    "ingredient_price" => $ingredient["ingredient_price"],
                    "orderQuantiy" => $orderValue["quantity"]
                   ]);
                }
            }
        }

        $orderPrice = 0;
        if(count($ingredientIsExists) == count($newOrder)){
            $order = new Order;
            $order->order_price = $orderPrice;
            $order->save();
            $orderId = $order->id;
            $description = "";
            foreach ($ingredientIsExists as $ingredientValue) {
                $orderData = new OrderData;
                $orderData->order_id = $orderId;
                $orderData->ingredient_id = $ingredientValue["ingredient_id"];
                $orderData->save();
                $description .= $ingredientValue["ingredient_name"]." ";
                $orderPrice += ($ingredientValue["ingredient_price"]*$ingredientValue["orderQuantiy"]);
            }
            $description = rtrim($description, " ");
            Order::where('order_id', $orderId)
            ->update(['order_price' => $orderPrice]);

            return ["description" => $description, "cost" => $orderPrice];
        }else{

            $diffIngredient = array_diff(array_column($newOrder, "name"), array_column($ingredientIsExists, "ingredient_name"));
            $diffIngredient = array_values($diffIngredient)[0];
            return response()->json(['message' => 'A '.$diffIngredient.' összetevő nem létezik.'], 500);
        }

    }
}
