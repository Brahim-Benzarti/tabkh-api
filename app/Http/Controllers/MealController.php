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
    function listMealsRaw($country){
        $data= Meal::where("countrycode",$country)->get();
        return response()->json($data, 200, $this->headers);
    }

    function listMeals(Request $request, $country){
        $data=["name","time","ingredients","steps"];
        if($request->query->get('pictures')){
            array_splice($data,1,0,"picture");
        }
        if($request->query->get('calories')){
            array_push($data,"total_calories");
            return response()->json(Meal::where("countrycode",$country)->whereBetween('total_calories',[$request->query->get('min-calories')??0,$request->query->get('max-calories')??10000000000])->get($data), 200, $this->headers);
        }

        return response()->json(Meal::where("countrycode",$country)->get($data), 200, $this->headers);
    }

    function findMeal(Request $request, $country, $name){
        $data=["name","time","ingredients","steps"];
        if($request->query->get('pictures')){
            array_splice($data,1,0,"picture");
        }
        if($request->query->get('calories')){
            array_push($data,"total_calories");
        }

        if(count(Meal::where("countrycode",$country)->where("name",$name)->get())){
            return response()->json(Meal::where("countrycode",$country)->where("name",$name)->get($data), 200, $this->headers);
        }

        $res=[];
        foreach (Meal::where("countrycode",$country)->get(["name"]) as $value) {
            similar_text($value["name"],$name,$percent);
            if($percent>75){array_push($res,$value["name"]);}
        }
        return response()->json(["Message"=>"Nothing found!","Similar"=>$res], 200, $this->headers);
    }

    public function listCountries(){
        $data=[];
        foreach(Meal::groupBy("countrycode")->get(["countrycode"]) as $item){
            if($item->countrycode){
                array_push($data, $item->countrycode);
            }
        }
        return response()->json($data, 200, $this->headers);
    }


    public function listCategories(){
        $data=[];
        foreach(Meal::groupBy("category")->get(["category"]) as $item){
            if($item->category){
                array_push($data, $item->category);
            }
        }
        return response()->json($data, 200, $this->headers);
    }


    public function testing(){
        return response()->json(["message"=>"okay"], 200, $this->headers);
    }
}
