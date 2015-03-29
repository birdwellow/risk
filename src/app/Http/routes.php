<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

//Route::get('/', 'WelcomeController@index');
Route::get('/', 'MatchController@overview');

//Route::get('home', 'HomeController@index');
Route::get('home', 'MatchController@overview');

Route::get('match/overview', 'MatchController@overview');
Route::get('match/new', 'MatchController@init');
Route::post('match/create', 'MatchController@create');
Route::get('match/{id}', 'MatchController@match');
Route::get('match/cancel/{id}', 'MatchController@cancel');

Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
