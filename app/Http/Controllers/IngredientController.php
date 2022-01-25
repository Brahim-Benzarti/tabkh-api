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

    public function addIngredient(Request $request){

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

    public function updateIngredient($id){
        
    }
}
