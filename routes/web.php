<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\MealController;
use App\Http\Controllers\IngredientController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::middleware(['auth:sanctum', 'verified'])->get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::middleware(['auth:sanctum', 'verified'])->group(function () {
    Route::get('/create-recepy', [MealController::class, 'mealIndex'])->name('create-recipe-index');
    Route::get('/add-ingredient', [IngredientController::class, 'ingredientIndex'])->name('add-ingredient-index');
});

//API Documentation
Route::get('/docs', function(){
    return redirect('https://documenter.getpostman.com/view/17915773/UVXqFtRg');
});