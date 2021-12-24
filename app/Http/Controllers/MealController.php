<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Meal;
use Illuminate\Support\Facades\DB;

class MealController extends Controller
{
    private $headers=["Content-Type"=>"application/json"];

    // Web
    function mealIndex(){
        return view("createrecipe");
    }



    // api
    function listMealsRaw(){
        $data= Meal::all();
        return response()->json($data, 200, $this->headers);
    }

    function listMeals(Request $request){
        $data=["name","time","ingredients","steps"];
        if($request->query->get('pictures')){
            array_splice($data,1,0,"picture");
        }
        if($request->query->get('calories')){
            array_push($data,"total_calories");
            return response()->json(Meal::all($data)->whereBetween('total_calories',[$request->query->get('min-calories')??0,$request->query->get('max-calories')??10000000000]), 200, $this->headers);
        }

        return response()->json(Meal::all($data), 200, $this->headers);
    }

    function findMeal(Request $request,$name){
        $data=["name","time","ingredients","steps"];
        if($request->query->get('pictures')){
            array_splice($data,1,0,"picture");
        }
        if($request->query->get('calories')){
            array_push($data,"total_calories");
        }

        if(count(Meal::where("name",$name)->get())){
            return response()->json(Meal::select($data)->where("name",$name)->get(), 200, $this->headers);
        }

        $res=[];
        foreach (Meal::all("name") as $value) {
            similar_text($value["name"],$name,$percent);
            if($percent>75){array_push($res,$value["name"]);}
        }
        return response()->json(["Message"=>"Nothing found!","Similar"=>$res], 200, $this->headers);
    }

}
