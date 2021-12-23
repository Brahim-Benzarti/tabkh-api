<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;

class MealController extends Controller
{
    function mealIndex(){
        return view("createrecipe");
    }

    function listMeals(){
        return 1;
    }

    function findMeal(){
        return 1;
    }

}
