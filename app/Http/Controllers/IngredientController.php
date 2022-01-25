<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\Models\Ingredient;

class IngredientController extends Controller
{
    private $headers=["Content-Type"=>"application/json"];

    // web
    function ingredientIndex(){
        return view("addingredient");
    }


    // api
    function listIngredientsRaw(){
        $data=Ingredient::all();
        return response()->json($data, 200, $this->headers);
    }

    function listIngredients(Request $request){
        $fields=["name","price","unit","description"];
        if($request->query->get('pictures')){
            array_splice($fields,1,0,"picture");
        }

        if($request->query->get('nutrition')){
            array_push($fields,"fat","protein","carbohydrates");
        }

        if($request->query->get('calories')){
            array_push($fields,"total_calories");
        }
        $data=Ingredient::all($fields);
        return response()->json($data, 200, $this->headers);
    }

    function findIngredients(Request $request,$name){
        $fields=["name","price","unit","description"];
        if($request->query->get('pictures')){
            array_splice($fields,1,0,"picture");
        }

        if($request->query->get('nutrition')){
            array_push($fields,"fat","protein","carbohydrates");
        }

        if($request->query->get('calories')){
            array_push($fields,"total_calories");
        }
        if(count(Ingredient::where("name",$name)->get())){
            return response()->json(Ingredient::select($fields)->where('name',$name)->get(), 200, $this->headers);
        }

        $res=[];
        foreach (Ingredient::all("name") as $value) {
            similar_text($value["name"],$name,$percent);
            if($percent>75){array_push($res,$value["name"]);}
        }
        return response()->json(["Message"=>"Nothing found!","Similar"=>$res], 200, $this->headers);
    }

    public function addIngredient(){
        $this->validate($request,[
            "name"=>'required|max:20|string',
            "picture"=>"sometimes|mimes:png,jpg,gif,jpeg|max:5000",
            "picture"=>"sometimes|active_url",
            "price"=>'required|numeric',
            "fat"=>"required|numeric",
            "protein"=>"required|numeric",
            "carbohydrates"=>"required|numeric",
            "hm"=>"required",
            "description"=>"required|string|max:5000",
            "unit"=>"required|exists:App\Models\Unit,abbreviation"
        ]);
        $ingredient = new Ingredient();
        $ingredient->creatorId=Auth::user()->id;
        $ingredient->name=$request->name;
        $ingredient->lname=$request->lname;
        $ingredient->unit=$request->unit;
        if($request->picture_url){
            $ingredient->picture=$request->url;
        }elseif($request->picture){
            // dd($request->picture);
            $picname=$request->picture->getFilename();
            // dd($request->picture);
            // dd(public_path("ok/"));
            file_put_contents(public_path('ingredients\\').$picname,file_get_contents($request->picture->getRealPath()));
            // Storage::disk('ingredients')->put($picname, file_get_contents($request->picture->getRealPath()));
            $ingredient->picture=public_path('ingredients\\').$picname;
        }
        $ingredient->price=(double)$request->price;
        $ingredient->fat=(double)$request->fat;
        $ingredient->protein=(double)$request->protein;
        $ingredient->carbohydrates=(double)$request->carbohydrates;
        $ingredient->home_made=$request->hm;
        $ingredient->description=$request->description;
        if($request->facts){
            $ingredient->facts=$request->facts;
        }
        if($request->uses){
            $ingredient->uses=$request->uses;
        }
        $ingredient->total_calories=$request->calcCalories();
        $ingredient->save();
    }

    public function deleteIngredient($id){
        $ingredient=Ingredient::find($id);
        if($ingredient){
            if($ingredient->creatorId==Auth::id()){
                $ingredient->delete();
                return response()->json(["message"=>"Ingredient deleted."], 200);
            }return response()->json(["message"=>"Your're not the owner."], 200);
        }
        return response()->json(["message"=>"No such Ingredient."], 200);
    }


    public function updateIngredient(Request $request, $id){
        $ingredient=Ingredient::find($id);
        if($ingredient){
            if($ingredient->creatorId==Auth::id()){
                if($request->method()=="PUT"){
                    foreach ($request->query as $key => $value) {
                        if(!$ingredient[$key]){
                            return response()->json(["message"=>"Some or all params don't exist."], 403);
                        }
                    }
                    foreach ($request->query as $key => $value) {
                        $this->validate($request, [
                            "name"=>'sometimes|max:20|string',
                            // "picture"=>"sometimes|mimes:png,jpg,gif,jpeg|max:5000",
                            "price"=>'sometimes|numeric',
                            "fat"=>"sometimes|numeric",
                            "protein"=>"sometimes|numeric",
                            "carbohydrates"=>"sometimes|numeric",
                            "hm"=>"sometimes",
                            "description"=>"sometimes|string|max:5000",
                            "unit"=>"sometimes|exists:App\Models\Unit,abbreviation"
                        ]);
                        $ingredient[$key]=$value;
                        $ingredient->save();
                    }
                }else{
                    foreach ($request->query as $key => $value) {
                        if(!$ingredient[$key]){
                            return response()->json(["message"=>"The specified parameter (".$key.") don't exist."], 403);
                        }
                    }
                    if(count($request->query)==1){
                        foreach ($request->query as $key => $value) {
                            $this->validate($request, [
                                "name"=>'sometimes|max:20|string',
                                // "picture"=>"sometimes|mimes:png,jpg,gif,jpeg|max:5000",
                                "price"=>'sometimes|numeric',
                                "fat"=>"sometimes|numeric",
                                "protein"=>"sometimes|numeric",
                                "carbohydrates"=>"sometimes|numeric",
                                "hm"=>"sometimes",
                                "description"=>"sometimes|string|max:5000",
                                "unit"=>"sometimes|exists:App\Models\Unit,abbreviation"
                            ]);
                            $ingredient[$key]=$value;
                            $ingredient->save();
                        }

                    }
                    return response()->json(["message"=>"One and only one parameter is allowed, yet you entered ".count($request->query)."."], 403);
                }
                return response()->json(["message"=>"Ingredient Updated."], 201);
            }return response()->json(["message"=>"Your're not the owner."], 403);
        }
        return response()->json(["message"=>"No such ingredient."], 404);
    }
}
