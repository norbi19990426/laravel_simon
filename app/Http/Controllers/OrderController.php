<?php

namespace App\Http\Controllers;
use App\Models\Ingredient;
use App\Models\OrderData;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function orderCreate(Request $request){
        $newOrder = $request->all();
        $ingredients = Ingredient::all();
        $ingredientIsExists = $this->isExistsIngredient($newOrder, $ingredients);

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
                $orderData->quantity = $ingredientValue["orderQuantiy"];
                $orderData->ingredient_id = $ingredientValue["ingredient_id"];
                $orderData->save();
                $description .= $ingredientValue["ingredient_name"]." ";
                $orderPrice += ($ingredientValue["ingredient_price"]*$ingredientValue["orderQuantiy"]);
            }
            $description = rtrim($description, " ");
            Order::where('order_id', $orderId)
            ->update(['order_price' => $orderPrice, "description" => $description]);

            return response()->json(["description" => $description, "cost" => $orderPrice]);
        }else{

            $diffIngredient = array_diff(array_column($newOrder, "name"), array_column($ingredientIsExists, "ingredient_name"));
            $diffIngredient = array_values($diffIngredient)[0];
            return response()->json(['message' => 'A '.$diffIngredient.' összetevő nem létezik.'], 500);
        }

    }
    public function orderList(){
        $order = Order::all();

        return response()->json($order);
    }
    public function orderDelete(Request $request, int $orderId){
        $order = Order::where('order_id',$orderId)->get();
        if(count($order) != 0){
            Order::where('order_id',$orderId)->delete();
            OrderData::where('order_id',$orderId)->delete();
        }else{
            return response()->json(['message' => 'A rendelés nem létezik.'], 500);  
        }
    }
    public function orderUpdate(Request $request, int $orderId){
        $ingredients = Ingredient::all();
        $updateOrder = $request->all();
        $ingredientIsExists = $this->isExistsIngredient($updateOrder, $ingredients);
        if(count($ingredientIsExists) == count($updateOrder)){ 
            $order = Order::where('order_id',$orderId)->get();
            if(count($order)){
                OrderData::where('order_id',$orderId)->delete();
                
                foreach ($updateOrder as $updateOrderKey => $updateOrderValue) {
                    foreach ($ingredients as $ingredientValue) {
                        if($ingredientValue["ingredient_name"] == $updateOrderValue["name"]){
                            $updateOrder[$updateOrderKey] = ["ingredient_id" => $ingredientValue["ingredient_id"], "ingredient_name" => $updateOrderValue["name"], "quantity" => $updateOrderValue["quantity"], "ingredient_price" => $ingredientValue["ingredient_price"]];
                        }
                    }
                } 
                $description = "";
                $orderPrice = 0;
                foreach ($updateOrder as $updateOrderValue) {
                    $orderData = new OrderData;
                    $orderData->order_id = $orderId;
                    $orderData->quantity = $updateOrderValue["quantity"];
                    $orderData->ingredient_id = $updateOrderValue["ingredient_id"];
                    $orderData->save();
                    $description .= $updateOrderValue["ingredient_name"]." ";
                    $orderPrice += ($updateOrderValue["ingredient_price"]*$updateOrderValue["quantity"]);
                }
                $description = rtrim($description, " ");
                Order::where('order_id', $orderId)
                ->update(['order_price' => $orderPrice, "description" => $description]);

                return response()->json(["description" => $description, "cost" => $orderPrice]);
            }
        }else{
            $diffIngredient = array_diff(array_column($updateOrder, "name"), array_column($ingredientIsExists, "ingredient_name"));
            $diffIngredient = array_values($diffIngredient)[0];
            return response()->json(['message' => 'A '.$diffIngredient.' összetevő nem létezik.'], 500);
        }
    }
    public function orderBasedOnIngredient($ingredientId){
        $orders = Order::join('order_data','order.order_id','=','order_data.order_id')->where('order_data.ingredient_id', $ingredientId)->get('order.*');

        return response()->json($orders);
    }
    public function isExistsIngredient($requestOrder, $ingredients){
        $ingredientIsExists = [];

        foreach ($requestOrder as $requestValue) {
            foreach ($ingredients as $ingredient) {
                if($ingredient["ingredient_name"] == $requestValue["name"]){
                   array_push($ingredientIsExists, 
                   [
                    "ingredient_id" => $ingredient["ingredient_id"],
                    "ingredient_name" => $ingredient["ingredient_name"],
                    "ingredient_price" => $ingredient["ingredient_price"],
                    "orderQuantiy" => $requestValue["quantity"]
                   ]);
                }
            }
        }
        return $ingredientIsExists;
    }
    
}
