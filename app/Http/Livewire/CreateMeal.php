<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meal;
use App\Models\Ingredient;
use Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Http\Request;

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
    // only for output
    public $beautyfilledingredients=[];

    public function render()
    {
        $this->ingredients= app("App\Http\Controllers\IngredientController")->listIngredients(new Request())->getOriginalContent();
        // dd(app("App\Http\Controllers\IngredientController")->listIngredients()->getOriginalContent());
        return view('livewire.create-meal');
    }

    protected $rules=[
        "name"=>["required","max:20","string"],
        "picture"=>["required","mimes:png,jpg,gifjpeg","max:5000"],
        "filledingredients"=>["required"],
        // "cost"=>["sometimes","numeric"],
        "steps"=>["required","string","min:10"],
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
        $picname=$this->picture->getFilename();
        file_put_contents(public_path('meals\\').$picname,file_get_contents($this->picture->getRealPath()));
        $meal->picture=public_path('meals\\').$picname;
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
        if($this->tutorials){
            $meal->tutorials=$this->tutorials;
        }
        if($this->backstory){
            $meal->backstory=$this->backstory;
        }
        $meal->total_calories=0;
        foreach ($this->filledingredientsraw as $value) {
            // dd($value);
            $meal->total_calories+=((int)$value["ingredient"]["total_calories"]/100)*(int)$value["quantity"];
        }
        $meal->save();
        $this->emit('saved');
        // cleaning after insert
        // $this->name=null;
        // $this->picture=null;
        // $this->time=null;
        // $this->filledingredients = [];
        // $this->ingno= 0;
        // $this->steps=null;
        // $this->cost=null;
        // $this->newingid=null;
        // $this->newingqt=null;
        // $this->beautyfilledingredients=[];
    }

    public function addIng(){
        // dd($this->ingredients);
        $this->validate([
            "newingid"=>["required","exists:App\Models\Ingredient,_id"],
            "newingqt"=>["required","numeric","max:10000"]
        ]);
        // dd(Ingredient::where('_id',$this->newingid)->select("name","unit")->get()[0]->name);
        $this->filledingredientsraw[$this->ingno] = ["ingredient"=>Ingredient::find($this->newingid), "quantity"=>$this->newingqt];
        $ing=Ingredient::where('_id',$this->newingid)->select("name","unit")->get()[0];
        $this->filledingredients[$this->ingno] = ["ingredient"=>$ing->name, "quantity"=>$this->newingqt." ".$ing->unit];
        $inginstance=$this->ingredients->find($this->newingid);
        $this->beautyfilledingredients[$this->ingno] = ["id"=>$this->ingno, "name"=>$inginstance->name, "quantity"=>$this->newingqt, "unit"=>"gram(s)"];
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
}
