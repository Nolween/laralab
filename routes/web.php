<?php

use Illuminate\Support\Facades\Route;

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
})->name('home');
Route::get('/exploration', function () {
    return view('exploration');
})->name('exploration.list');
Route::get('/my-notebook', function () {
    return view('mynotebook');
})->name('my-notebook.list');
Route::get('/my-recipes', function () {
    return view('myrecipes');
})->name('my-recipes.list');
Route::get('/recipe/view/{id}', function ($i) {
    return view('recipeview');
})->name('my-recipes.view');
Route::get('/recipe/new', function () {
    return view('recipenew');
})->name('my-recipes.new');
Route::get('/recipe/edit/{id}', function ($id) {
    return view('recipeedit');
})->name('my-recipes.edit');
