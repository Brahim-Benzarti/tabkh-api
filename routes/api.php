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
    Route::post('/add_ingredient', [IngredientController::class, 'addIngredient'])->name('add-meal');
    Route::post('/add_unit', [UnitController::class, 'addUnit'])->name('add-unit');
});

Route::middleware(['auth:sanctum','ability:delete'])->group(function(){
    Route::post('/delete_meal/{id}', [MealController::class, 'deleteMeal'])->name('delete-meal');
    Route::post('/delete_ingredient/{id}', [IngredientController::class, 'deleteIngredient'])->name('delete-meal');
    Route::post('/delete_unit/{id}', [UnitController::class, 'deleteUnit'])->name('delete-unit');
});

Route::middleware(['auth:sanctum','ability:update'])->group(function(){
    Route::post('/update_meal/{id}', [MealController::class, 'updateMeal'])->name('update-meal');
    Route::post('/update_ingredient/{id}', [IngredientController::class, 'updateIngredient'])->name('update-meal');
    Route::post('/update_unit/{id}', [UnitController::class, 'updateUnit'])->name('update-unit');
});

Route::middleware(['auth:sanctum','ability:read'])->group(function(){
    Route::get('/meals-raw', [MealController::class, 'listMealsRaw']);
    Route::get('/meals', [MealController::class, 'listMeals'])->name('list-ingredients');
    Route::get('/meal/{name}', [MealController::class, 'findMeal'])->name('find-ingredient');

    Route::get('/ingredients-raw', [IngredientController::class, 'listIngredientsRaw'])->name('list-ingredients');
    Route::get('/ingredients', [IngredientController::class, 'listIngredients'])->name('list-ingredients');
    Route::get('/ingredient/{name}', [IngredientController::class, 'findIngredients'])->name('find-ingredient');
});