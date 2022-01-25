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

// API reqs -- these endpoints are only for the API CRUD.. 
Route::middleware(['auth:sanctum','ability:create'])->group(function(){
    Route::post('/add_recipe', [MealController::class, 'addMeal'])->name('add-meal');
    Route::post('/add_ingredient', [IngredientController::class, 'addIngredient'])->name('add-meal');
    Route::post('/add_unit', [UnitController::class, 'addUnit'])->name('add-unit');
});

Route::middleware(['auth:sanctum','ability:delete'])->group(function(){
    Route::delete('/delete_recipe/{id}', [MealController::class, 'deleteMeal'])->name('delete-meal');
    Route::delete('/delete_ingredient/{id}', [IngredientController::class, 'deleteIngredient'])->name('delete-meal');
    Route::delete('/delete_unit/{id}', [UnitController::class, 'deleteUnit'])->name('delete-unit');
});

Route::middleware(['auth:sanctum','ability:update'])->group(function(){
    Route::match(['put', 'patch'],'/update_recipe/{id}', [MealController::class, 'updateMeal'])->name('update-meal');
    Route::match(['put', 'patch'],'/update_ingredient/{id}', [IngredientController::class, 'updateIngredient'])->name('update-meal');
    Route::match(['put', 'patch'],'/update_unit/{id}', [UnitController::class, 'updateUnit'])->name('update-unit');
});

Route::middleware(['auth:sanctum','ability:test'])->group(function(){
    Route::get('/test', [MealController::class, 'testing']);
});
// end API reqs

Route::middleware(['auth:sanctum','ability:read'])->group(function(){
    Route::prefix('{country}')->group(function () {
        Route::get('/recipes-raw', [MealController::class, 'listMealsRaw']);
    });
    Route::get('/ingredients-raw', [IngredientController::class, 'listIngredientsRaw'])->name('list-ingredients');
});



// public apis 

Route::get('/countries', [MealController::class, 'listCountries']);
Route::get('/categories', [MealController::class, 'listCategories']);
Route::prefix('{country}')->group(function () {
    Route::get('/recipes', [MealController::class, 'listMeals'])->name('list-ingredients');
    Route::get('/recipe/{name}', [MealController::class, 'findMeal'])->name('find-ingredient');
});
Route::get('/ingredients', [IngredientController::class, 'listIngredients'])->name('list-ingredients');
Route::get('/ingredient/{name}', [IngredientController::class, 'findIngredients'])->name('find-ingredient');

//API Documentation
Route::get('/docs', function(){
    return redirect('https://documenter.getpostman.com/view/17915773/UVXqFtRg');
});