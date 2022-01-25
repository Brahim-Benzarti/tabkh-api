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


    public function addMeal(Request $request){
        $this->validate($request, [
            "name"=>["required","max:20","string"],
            "lname"=>["sometimes","max:20","string"],
            "category"=>["sometimes","string","max:100"],
            "time"=>["required","numeric","min:1","max:300"],

            // "picture"=>["required","mimes:png,jpg,gifjpeg","max:5000"],
            "filledingredients"=>["required"],
            // "cost"=>["sometimes","numeric"],
            "steps"=>["required","string","min:10"],
            // "backstory"=>["sometimes","string","max:500"]
        ]);

        $meal = new Meal();
        $meal->creatorId=Auth::user()->id;
        $meal->name=$request->name;
        $meal->lname=$request->lname;
        if($request->category){
            $meal->category=$request->category;
        }
        if($request->country_code){
            if($request->country_code && $request->country_name){
                $meal->country=$request->country_name;
                $meal->countrycode=$request->country_code;
            }else{
                $meal->countrycode=$request->country_code;
            }
        }
        if($request->picture_url){
            $meal->picture=$request->picture_url;
        }elseif($request->picture){
            $picname=$request->picture->getFilename();
            file_put_contents(public_path('meals\\').$picname,file_get_contents($request->picture->getRealPath()));
            $meal->picture=public_path('meals\\').$picname;
        }
        $meal->time=$request->time;
        $filledingredientsraw=[];
        $i=0;
        foreach ($request->ingredients as $value) {
            $ing=Ingredients::find($value["ingredient"]);
            if(!$ing){
                return response()->json(["message"=>"One or many ingredients don't exist."], 404);
            }
            $filledingredientsraw[$i++]=[
                "ingredient"=>$ing,
                "quantity"=>$value["value"],
                "unit"=>$value["unit"]
            ];
        }
        $meal->ingredients_raw=$filledingredientsraw;
        $meal->steps=$request->steps;
        $meal->lsteps=$request->lsteps;
        if($request->cost){
            $meal->cost=(int)$request->cost;
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
        $meal->total_calories=0;
        foreach ($request->filledingredientsraw as $value) {
            $tempunit=Unit::where("abbreviation",$value["ingredient"]["unit"])->get()[0]->equivalents;
            if($tempunit){
                $meal->total_calories+=(($value["ingredient"]["total_calories"]/100)/$tempunit[$value["unit"]])*$value["quantity"];
            }else{
                response()->json(["message"=>"The unit specified is not supported."], 404);
            }
        }
        $meal->save();
        return response()->json(["message"=>"Recipe created successfully."], 200);
    }

    public function deleteMeal($id){
        $meal=Meal::find($id);
        if($meal){
            if($meal->creatorId==Auth::id()){
                $meal->delete();
                return response()->json(["message"=>"Recipe deleted."], 201);//success
            }return response()->json(["message"=>"Your're not the owner."], 403);//forbidden
        }
        return response()->json(["message"=>"No such recipe."], 404);//not found
    }
    

    public function updateMeal(Request $request, $id){
        $meal=Meal::find($id);
        if($meal){
            if($meal->creatorId==Auth::id()){
                if($request->method()=="PUT"){
                    foreach ($request->query as $key => $value) {
                        if(!$meal[$key]){
                            return response()->json(["message"=>"Some or all params don't exist."], 403);
                        }
                    }
                    foreach ($request->query as $key => $value) {
                        $this->validate($request, [
                            "name"=>["sometimes","max:20","string"],
                            // "picture"=>["required","mimes:png,jpg,gifjpeg","max:5000"],
                            // "filledingredients"=>["sometimes"],
                            "cost"=>["sometimes","numeric"],
                            "steps"=>["sometimes","string","min:10"],
                            "backstory"=>["sometimes","string","max:500"]
                        ]);
                        $meal[$key]=$value;
                        $meal->save();
                    }
                }else{
                    foreach ($request->query as $key => $value) {
                        if(!$meal[$key]){
                            return response()->json(["message"=>"The specified parameter (".$key.") don't exist."], 403);
                        }
                    }
                    if(count($request->query)==1){
                        foreach ($request->query as $key => $value) {
                            $this->validate($request, [
                                "name"=>["sometimes","max:20","string"],
                                // "picture"=>["required","mimes:png,jpg,gifjpeg","max:5000"],
                                // "filledingredients"=>["sometimes"],
                                "cost"=>["sometimes","numeric"],
                                "steps"=>["sometimes","string","min:10"],
                                "backstory"=>["sometimes","string","max:500"]
                            ]);
                            $meal[$key]=$value;
                            $meal->save();
                        }

                    }
                    return response()->json(["message"=>"One and only one parameter is allowed, yet you entered ".count($request->query)."."], 403);
                }
                return response()->json(["message"=>"Recipe Updated."], 201);
            }return response()->json(["message"=>"Your're not the owner."], 403);
        }
        return response()->json(["message"=>"No such recipe."], 404);
    }


    public function testing(){
        return response()->json(["message"=>"okay"], 200, $this->headers);
    }
}
