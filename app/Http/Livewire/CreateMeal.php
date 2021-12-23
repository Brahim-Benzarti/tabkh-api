<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meal;
use Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;

class CreateMeal extends Component
{
    use WithFileUploads;
    public $name;
    public $picture;
    public $time;
    public $ingredients= [];
    public $filledingredients = [];
    public $ingno= 0;
    public $steps;
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
        $this->ingredients= app("App\Http\Controllers\IngredientController")->listIngredients()->getOriginalContent();
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
        // $headers=["Content-Type"=>"application/json"];
        $this->validate($this->rules);
        try {
            $meal = new Meal();
            $meal->creatorId=Auth::user()->id;
            $meal->name=$this->name;
            $meal->picture=$this->picture;
            if($this->time){
                $meal->time=$this->time;
            }
            $meal->ingredients=$this->filledingredients;
            $meal->steps=$this->steps;
            if($this->cost){
                $meal->cost=$this->cost;
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
            $meal->save();
            $this->emit('saved');
            // cleaning after insert
            $this->name=null;
            $this->picture=null;
            $this->time=null;
            $this->filledingredients = [];
            $this->ingno= 0;
            $this->steps=null;
            $this->cost=null;
            $this->ready=null;
            $this->tutorials=null;
            $this->backstory=null;
            $this->newingid=null;
            $this->newingqt=null;
            $this->beautyfilledingredients=[];
            // return response()->json(["message"=>"Meal added successfully"], 200, $headers);
        } catch (\Throwable $th) {
            // 406 not acceptable
            // return response()->json(["message"=>$th], 406, $headers);
            $this->emit('error');
        }
    }

    public function addIng(){
        // dd($this->ingredients);
        $this->validate([
            "newingid"=>["required","exists:App\Models\Ingredient,_id"],
            "newingqt"=>["required"]
        ]);
        $this->filledingredients[$this->ingno] = ["id"=>$this->newingid, "quantity"=>$this->newingqt];
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
        unset($this->beautyfilledingredients[$ingnotrm]);
    }
}
