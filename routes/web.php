<?php

/** @var \Laravel\Lumen\Routing\Router $router */

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/
use Illuminate\Support\Facades\Route;

$router->get('/', function () use ($router) {
    return $router->app->version();
});

Route::group(['prefix' => 'api/v1'], function () {

    Route::get('/', function(){
        return "API";
    });

    Route::group(["prefix" => "authentication"], function () {
        Route::post("login", "AuthenticationController@login");
        Route::post("register", "AuthenticationController@register");
    });
});
