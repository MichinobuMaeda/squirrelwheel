<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\TemplateController;
use App\Http\Controllers\ArticleController;
use App\Http\Controllers\SqwhAuthController;

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

Route::redirect('/', 'articles');
Route::get('/login', [SqwhAuthController::class, 'login'])
    ->name('login');
Route::get('/auth/mastodon', [SqwhAuthController::class, 'mastodon'])
    ->name('auth.mastodon');

Route::group(['middleware' => 'auth'], function () {
    Route::view('/me', 'auth.me')->name('me');

    Route::put('/categories/{category}/enable', [CategoryController::class, 'enable'])
        ->withTrashed()
        ->name('categories.enable');
    Route::put('/categories/{category}/disable', [CategoryController::class, 'disable'])
        ->withTrashed()
        ->name('categories.disable');
    Route::delete('/categories/{category}/destroy', [CategoryController::class, 'destroy'])
        ->withTrashed()
        ->name('categories.destroy');
    Route::resource('categories', CategoryController::class)
        ->withTrashed()
        ->except(['show', 'destroy'])
        ->middleware('auth');

    Route::put('/templates/{template}/enable', [TemplateController::class, 'enable'])
        ->withTrashed()
        ->name('templates.enable');
    Route::put('/templates/{template}/disable', [TemplateController::class, 'disable'])
        ->withTrashed()
        ->name('templates.disable');
    Route::delete('/templates/{template}/destroy', [CategoryController::class, 'destroy'])
        ->withTrashed()
        ->name('templates.destroy');
    Route::resource('templates', TemplateController::class)
        ->withTrashed()
        ->except(['show', 'destroy'])
        ->middleware('auth');

    Route::put('/articles/{article}/enable', [ArticleController::class, 'enable'])
        ->withTrashed()
        ->name('articles.enable');
    Route::put('/articles/{article}/disable', [ArticleController::class, 'disable'])
        ->withTrashed()
        ->name('articles.disable');
    Route::delete('/articles/{article}/destroy', [CategoryController::class, 'destroy'])
        ->withTrashed()
        ->name('articles.destroy');
    Route::resource('articles', ArticleController::class)
        ->withTrashed()
        ->except(['show', 'destroy'])
        ->middleware('auth');
});
