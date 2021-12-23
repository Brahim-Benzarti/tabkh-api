<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MealController;
use App\Http\Controllers\IngredientController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->group(function (){
    Route::get('/user', function (Request $request) {
        return $request->user();
    });
});

Route::middleware(['auth:sanctum','ability:create'])->group(function(){
    Route::post('/add_meal', [MealController::class, 'addMeal'])->name('add-meal');
});

Route::middleware(['auth:sanctum','ability:read'])->group(function(){
    Route::get('/meals', [MealController::class, 'listMeals'])->name('list-ingredients');
    Route::get('/meal/{name}', [MealController::class, 'findMeal'])->name('find-ingredient');

    Route::get('/ingredients', [IngredientController::class, 'listIngredients'])->name('list-ingredients');
    Route::get('/ingredient/{name}', [IngredientController::class, 'findIngredients'])->name('find-ingredient');
});
