<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Unit;

class UnitController extends Controller
{
    public function addUnit(Request $request){
        $a=new Unit();
        $a->unit=$request->unit;
        $a->abbreviation=$request->abbreviation;
        $a->equivalents=$request->equivalents;
        $a->save();
        return response()->json(["message"=>"Unit added successfully!"], 200, ["Content-Type"=>"application/json"]);
    }
}
