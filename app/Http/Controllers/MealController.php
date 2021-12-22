<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meal;
use Auth;

class MealController extends Controller
{
    function addMeal(Request $request){
        $headers=["Content-Type"=>"application/json"];
        try {
            $meal = new Meal();
            $meal->creatorId=Auth::user()->id;
            $meal->name=$request->name;
            $meal->picture=$request->picture;
            if($request->time){
                $meal->time=$request->time;
            }
            $meal->ingredients=$request->ingredients;
            $meal->preparation=$request->preparation;
            if($request->cost){
                $meal->cost=$request->cost;
            }
            if($request->ready){
                $meal->ready=$request->ready;
            }
            if($request->tutorials){
                $meal->tutorials=$request->tutorials;
            }
            if($request->backstory){
                $meal->backstory=$request->backstory;
            }
            $meal->save();
            return response()->json(["message"=>"Meal added successfully"], 200, $headers);
        } catch (\Throwable $th) {
            // 406 not acceptable
            return response()->json(["message"=>$th], 406, $headers);
        }
    }
}
