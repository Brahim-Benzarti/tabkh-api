<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\Ingredient;
use App\Models\Unit;
use Auth;
use Livewire\WithFileUploads;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Storage;

class CreateIngridient extends Component
{
    use WithFileUploads;
    public $name;
    public $lname;
    public $picture;
    public $unit;
    public $price;
    public $fat;
    public $protein;
    public $carbohydrates;
    public $hm=false;
    public $description;
    public $units;
    public $facts;
    public $uses;

    protected $rules=[
        "name"=>'required|max:20|string',
        "picture"=>"required|mimes:png,jpg,gifjpeg|max:5000",
        "price"=>'required|numeric',
        "fat"=>"required|numeric",
        "protein"=>"required|numeric",
        "carbohydrates"=>"required|numeric",
        "hm"=>"required",
        "description"=>"required|string|max:5000",
        "unit"=>"required|exists:App\Models\Unit,abbreviation"
    ];

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }
    
    public function render()
    {
        $this->units=Unit::all();
        return view('livewire.create-ingridient');
    }

    public function addIngredient(){
        $this->validate($this->rules);
        $ingredient = new Ingredient();
        $ingredient->creatorId=Auth::user()->id;
        $ingredient->name=$this->name;
        $ingredient->lname=$this->lname;
        $ingredient->unit=$this->unit;
        // dd($this->picture);
        $picname=$this->picture->getFilename();
        // dd($this->picture);
        // dd(public_path("ok/"));
        file_put_contents(public_path('ingredients\\').$picname,file_get_contents($this->picture->getRealPath()));
        // Storage::disk('ingredients')->put($picname, file_get_contents($this->picture->getRealPath()));
        $ingredient->picture=public_path('ingredients\\').$picname;
        $ingredient->price=(double)$this->price;
        $ingredient->fat=(double)$this->fat;
        $ingredient->protein=(double)$this->protein;
        $ingredient->carbohydrates=(double)$this->carbohydrates;
        $ingredient->home_made=$this->hm;
        $ingredient->description=$this->description;
        if($this->facts){
            $ingredient->facts=$this->facts;
        }
        if($this->uses){
            $ingredient->uses=$this->uses;
        }
        $ingredient->total_calories=$this->calcCalories();
        $ingredient->save();
        $this->emit('saved');
        // $this->name=null;
        // $this->picture=null;
        // $this->price=null;
        // $this->fat=null;
        // $this->protein=null;
        // $this->carbohydrates=null;
        // $this->hm=false;
        // $this->description=null;
        // $this->unit=null;
    }

    public function calcCalories(){
        return (
            ($this->fat * 9) +
            ($this->protein * 4) +
            ($this->carbohydrates * 4)
        );
    }

}

