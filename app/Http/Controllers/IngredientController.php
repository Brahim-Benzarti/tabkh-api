<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    function ingredientIndex(){
        return view("addingredient");
    }

    function listIngredients(){
        $headers=["Content-Type"=>"application/json"];
        $data=Ingredient::all();
        return response()->json($data, 200, $headers);
    }
    function findIngredients(){
        return 1;
    }
}
