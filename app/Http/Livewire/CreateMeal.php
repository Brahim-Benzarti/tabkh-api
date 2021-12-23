<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Meal;
use Auth;
use Livewire\WithFileUploads;

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

    public function render()
    {
        $this->ingredients= app("App\Http\Controllers\IngredientController")->listIngredients()->getOriginalContent();
        // dd(app("App\Http\Controllers\IngredientController")->listIngredients()->getOriginalContent());
        return view('livewire.create-meal');
    }

    protected $rules=[
        "name"=>'required|max:20|string',
        "picture"=>"required|mimes:png,jpg,gifjpeg|max:5000",
        "time"=>"required|date:'H:i:s'",
        "cost"=>'sometimes|numeric',
        "steps"=>'required|string|min:10',
        "backstory"=>"sometimes|string|max:500"
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
            $meal->ingredients=$this->ingredients;
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
            // return response()->json(["message"=>"Meal added successfully"], 200, $headers);
        } catch (\Throwable $th) {
            // 406 not acceptable
            // return response()->json(["message"=>$th], 406, $headers);
            $this->emit('error');
        }
    }

    public function incr(){
        $this->ingno++;
    }
}
