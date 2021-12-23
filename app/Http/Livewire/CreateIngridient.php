<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ingredient;
use Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CreateIngridient extends Component
{
    use WithFileUploads;
    public $name;
    public $picture;
    public $price;
    public $fat;
    public $protein;
    public $carbohydrates;
    public $hm=0;
    public $description;

    protected $rules=[
        "name"=>'required|max:20|string',
        "picture"=>"required|mimes:png,jpg,gifjpeg|max:5000",
        "price"=>'required|numeric',
        "fat"=>"required|numeric",
        "protein"=>"required|numeric",
        "carbohydrates"=>"required|numeric",
        "hm"=>"required",
        "description"=>"required|string|max:500"

    ];

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }
    
    public function render()
    {
        return view('livewire.create-ingridient');
    }

    public function addIngredient(){
        $this->validate($this->rules);
        $ingredient = new Ingredient();
        $ingredient->creatorId=Auth::user()->id;
        $ingredient->name=$this->name;
        // dd($this->picture);
        $picname=$this->picture->getClientOriginalName();
        $this->picture->storeAs('ingredients',$picname);
        $ingredient->picture=Storage::disk('ingredients')->url($picname);
        $ingredient->price=$this->price;
        $ingredient->fat=$this->fat;
        $ingredient->protein=$this->protein;
        $ingredient->carbohydrates=$this->carbohydrates;
        $ingredient->home_made=$this->hm;
        $ingredient->description=$this->description;
        $ingredient->total_calories=$this->calcCalories();
        $ingredient->save();
        $this->emit('saved');
    }

    public function calcCalories(){
        return (
            ((int)$this->fat * 9) +
            ((int)$this->protein * 4) +
            ((int)$this->carbohydrates * 4)
        );
    }

}

