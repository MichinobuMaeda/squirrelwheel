<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ArticleController;

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

Route::group(['middleware' => 'doku'], function() {
    Route::redirect('/', '/articles');
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('templates', TemplateController::class)->except(['show']);
    Route::resource('articles', ArticleController::class)->except(['show']);
});
