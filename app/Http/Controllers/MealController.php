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
            "picture"=>["required","mimes:png,jpg,gifjpeg","max:5000"],
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
            if($request->category=="null" && $request->newcategory){
                $meal->category=$request->newcategory;
            }else{
                $meal->category=$request->category;
            }
        }
        if($request->country){
            if($request->country=="null" && $request->newcountry && $request->newcountrycode){
                $meal->country=$request->newcountry;
                $meal->countrycode=$request->newcountrycode;
            }else{
                $meal->countrycode=$request->country;
            }
        }
        $picname=$request->picture->getFilename();
        file_put_contents(public_path('meals\\').$picname,file_get_contents($request->picture->getRealPath()));
        $meal->picture=public_path('meals\\').$picname;
        if($request->time){
            $meal->time=$request->time;
        }
        $meal->ingredients=$request->filledingredients;
        $meal->ingredients_raw=$request->filledingredientsraw;
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
            $meal->total_calories+=(($value["ingredient"]["total_calories"]/100)/$tempunit[$value["unit"]])*$value["quantity"];
        }
        $meal->save();

        return response()->json(["message"=>"Recipe created successfully."], 200);
    }

    public function deleteMeal($id){
        $meal=Meal::find($id);
        if($meal){
            if($meal->creatorId==Auth::id()){
                $meal->delete();
                return response()->json(["message"=>"Recipe deleted."], 200);
            }return response()->json(["message"=>"Your're not the owner."], 200);
        }
        return response()->json(["message"=>"No such recipe."], 200);
    }

    public function updateMeal($i){
        
    }


    public function testing(){
        return response()->json(["message"=>"okay"], 200, $this->headers);
    }
}
