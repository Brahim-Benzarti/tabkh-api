<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Ingredient;
use App\Models\Unit;
use Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;
use Luka\Envoy\Facades\Worker;

class CreateMeal extends Component
{
    use WithFileUploads;
    public $name;
    public $lname;
    public $picture;
    public $time;
    public $ingredients= [];
    public $filledingredients = [];
    public $filledingredientsraw = [];
    public $ingno= 0;
    public $steps;
    public $lsteps;
    public $cost;
    public $ready;
    public $tutorials;
    public $backstory;
    public $newingid;
    public $newingqt;
    public $newingunit;
    public $units= [];
    public $newcategory;
    public $categories=[];
    public $category="null";
    public $newcountry;
    public $newcountrycode;
    public $countries=[];
    public $country="null";


    // only for output
    public $beautyfilledingredients=[];

    public function render()
    {
        $this->categories=[];
        $this->countries=[];
        $this->ingredients= app("App\Http\Controllers\IngredientController")->listIngredients(new Request())->getOriginalContent();
        foreach (Meal::groupBy("category")->get(["category"]) as $value) {
            if($value->category){
                array_push($this->categories, $value->category);
            }
        }
        foreach (Meal::groupBy("countrycode")->get(["countrycode", "country"]) as $value) {
            if($value->countrycode){
                $this->countries[$value->countrycode]=$value->country;
            }
        }
        // dd(app("App\Http\Controllers\IngredientController")->listIngredients()->getOriginalContent());
        return view('livewire.create-meal');
    }

    protected $rules=[
        "name"=>["required","max:20","string"],
        // "picture"=>["sometimes","mimes:png,jpg,gif,jpeg","max:5000"],
        "filledingredients"=>["required"],
        // "cost"=>["sometimes","numeric"],
        "steps"=>["required","string","min:10"],
        "picture"=>["required","string"],
        // "backstory"=>["sometimes","string","max:500"],
    ];

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function addMeal(){
        $this->validate($this->rules);
        $meal = new Meal();
        $meal->creatorId=Auth::user()->id;
        $meal->name=$this->name;
        $meal->lname=$this->lname;
        if($this->country){
            if($this->country=="null" && $this->newcountry && $this->newcountrycode){
                $meal->country=$this->newcountry;
                $meal->countrycode=$this->newcountrycode;
            }else{
                $meal->countrycode=$this->country;
            }
        }
        // if($this->picture){
        //     $picname=$this->picture->getFilename();
        //     file_put_contents(public_path('meals\\').$picname,file_get_contents($this->picture->getRealPath()));
        //     $meal->picture=public_path('meals\\').$picname;
        // }
        $meal->picture=$this->picture;
        if($this->time){
            $meal->time=$this->time;
        }
        $meal->ingredients=$this->filledingredients;
        $meal->ingredients_raw=$this->filledingredientsraw;
        $meal->steps=$this->steps;
        $meal->lsteps=$this->lsteps;
        if($this->cost){
            $meal->cost=(int)$this->cost;
        }
        if($this->ready){
            $meal->ready=$this->ready;
        }
        // if($this->tutorials){
        //     $meal->tutorials=$this->tutorials;
        // }
        $meal->tutorials="ytb-url";
        if($this->backstory){
            $meal->backstory=$this->backstory;
        }
        $meal->total_calories=0;
        foreach ($this->filledingredientsraw as $value) {
            $tempunit=Unit::where("abbreviation",$value["ingredient"]["unit"])->get()[0]->equivalents;
            $meal->total_calories+=(($value["ingredient"]["total_calories"]/100)/$tempunit[$value["unit"]])*$value["quantity"];
        }

        //Inferring the category from onthology

        //Retrieving the types of each recipe selected
        $ingredients="";
        for ($i=0; $i < count($this->filledingredientsraw); $i++) { 
            $formattedingredientname=implode(array_reverse(explode(" ",$this->filledingredientsraw[$i]["ingredient"]["category"])));
            $ingredients=$ingredients.$formattedingredientname."Ingredient";
            if ($i < (count($this->filledingredientsraw)-1)) {
                $ingredients=$ingredients.",";
            }
        }

        //generating insert query because the cli was crazy about it
        $myfile = fopen("insert_query.rq", "w") or die("Unable to open file!");
        $prefix="PREFIX : <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> PREFIX owl: <http://www.w3.org/2002/07/owl#> PREFIX rdf: <http://www.w3.org/1999/02/22-rdf-syntax-ns#> PREFIX xml: <http://www.w3.org/XML/1998/namespace> PREFIX xsd: <http://www.w3.org/2001/XMLSchema#> PREFIX rdfs: <http://www.w3.org/2000/01/rdf-schema#> BASE <http://www.semanticweb.org/banzo/ontologies/2022/5/dbara#> ";
        fwrite($myfile, $prefix);
        fwrite($myfile, "INSERT DATA { :".$this->name." rdf:type owl:Class ; rdfs:subClassOf :NamedRecipe , ");
        for ($i = 0; $i < count(explode(",",$ingredients)); $i++){
            fwrite($myfile, "[ rdf:type owl:Restriction ; owl:onProperty :hasIngredient ; owl:someValuesFrom :".explode(",",$ingredients)[$i]." ] , ");
            if($i==(count(explode(",",$ingredients))-1)){
                $union="(";
                foreach (explode(",",$ingredients) as $ingredientClass){
                    $union=$union." :".$ingredientClass." ";
                }
                $union=$union.")";
                fwrite($myfile,"[ rdf:type owl:Restriction ; owl:onProperty :hasIngredient ; owl:allValuesFrom [ rdf:type owl:Class ; owl:unionOf ".$union."]] .}");
            }        
        }
        fclose($myfile);

        //heavy lifting for the ontology
        $res=Worker::task('handleRecipeCreation')->arguments([""])->run();
        if($res!=""){
            dd($res);
        }

        $inferredcategory=[];
        $myfile = fopen("inferred_categories.csv", "r") or die("Unable to open file!");
        $atheader=true;
        while(!feof($myfile)) {
            if(!$atheader){
                array_push($inferredcategory,fgetcsv($myfile));
                // array_push($inferredcategory,explode(",",fgets($myfile))[1])
            }else{fgets($myfile);}
            $atheader=false;
        }
        fclose($myfile);

        $inferredcategories="";
        if (count($inferredcategory) >2) {
            for ($i=0; $i < count($inferredcategory)-2; $i++) { 
                $inferredcategories=$inferredcategories.str_replace("Recipe"," Recipe",explode("#",$inferredcategory[$i][1])[1]);
                if($i<(count($inferredcategory)-3)){
                    $inferredcategories=$inferredcategories.",";
                }
            }
        }
        
        if($inferredcategories!=""){
            $meal->category=$inferredcategories;
        }
        
        $meal->save();
        if($inferredcategories!=""){
            $this->emit('inferred');
        }else{
            $this->emit('saved');
        }
        // cleaning after insert
        $this->name=null;
        $this->picture=null;
        $this->time=null;
        $this->filledingredients = [];
        $this->ingno= 0;
        $this->steps=null;
        $this->cost=null;
        $this->newingid=null;
        $this->newingqt=null;
        $this->beautyfilledingredients=[];
    }

    public function addIng(){
        // dd($this->ingredients);
        $this->validate([
            "newingid"=>["required","exists:App\Models\Ingredient,_id"],
            "newingqt"=>["required","numeric","max:10000"],
            "newingunit"=>["required"]
        ]);
        // dd(Ingredient::where('_id',$this->newingid)->select("name","unit")->get()[0]->name);
        $this->filledingredientsraw[$this->ingno] = ["ingredient"=>Ingredient::find($this->newingid), "quantity"=>$this->newingqt, "unit"=>$this->newingunit];
        $ing=Ingredient::where('_id',$this->newingid)->select("name","unit")->get()[0];
        $this->filledingredients[$this->ingno] = ["ingredient"=>$ing->name, "quantity"=>$this->newingqt." ".$this->newingunit."(s)"];
        $inginstance=$this->ingredients->find($this->newingid);
        $this->beautyfilledingredients[$this->ingno] = ["id"=>$this->ingno, "name"=>$inginstance->name, "quantity"=>$this->newingqt, "unit"=>$this->newingunit];
        $this->newingid="null";
        $this->newingqt=null;
        $this->ingno++;
    }

    public function removeIng($ingnotrm){
        // $temp=[];
        // $temp2=[];
        // foreach ($this->filledingredients as $key => $value) {
        //     if($key==$ingnotrm){
        //         continue;
        //     }else{
        //         $temp[$key]=$value;
        //     }
        // }
        // foreach ($this->beautyfilledingredients as $key => $value) {
        //     if($key==$ingnotrm){
        //         continue;
        //     }else{
        //         $temp[$key]=$value;
        //     }
        // }
        // $this->filledingredients=$temp;
        // $this->beautyfilledingredients=$temp2;
        unset($this->filledingredients[$ingnotrm]);
        unset($this->filledingredientsraw[$ingnotrm]);
        unset($this->beautyfilledingredients[$ingnotrm]);
    }

    public function getUnits(){
        $this->units=[];
        if($this->newingid){
            $unit=Unit::where("abbreviation",Ingredient::find($this->newingid)->unit)->get()[0];
            foreach ($unit->equivalents as $key => $value) {
                array_push($this->units,$key);
                // dd($this->units);
            }
        }else{
            $units["message"]="choose first";
        }
    }
}
