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

Route::get('/', [
    'as' => 'index',
    'uses' => 'MatchController@index'
]);

Route::get('match/new', [
    'as' => 'match.new',
    'uses' => 'MatchController@init'
]);

Route::post('match/create', [
    'as' => 'match.create',
    'uses' => 'MatchController@create'
]);

Route::get('match/{id}', [
    'as' => 'match.join',
    'uses' => 'MatchController@match'
]);

Route::get('match/cancel/{id}', [
    'as' => 'match.cancel',
    'uses' => 'MatchController@cancel'
]);

Route::get('match/administrate/{id}', [
    'as' => 'match.administrate',
    'uses' => 'MatchController@administrate'
]);

Route::post('match/administrate/{id}', [
    'as' => 'match.administrate.save',
    'uses' => 'MatchController@saveAdministrate'
]);

Route::get('invitation/reject/{id}', [
    'as' => 'invitation.reject',
    'uses' => 'MatchController@rejectInvitation'
]);

Route::get('invitation/delete/{id}', [
    'as' => 'invitation.delete',
    'uses' => 'MatchController@deleteInvitation'
]);




Route::get('user/profile', [
    'as' => 'user.profile',
    'uses' => 'UserController@profile'
]);

Route::post('user/profile', [
    'as' => 'user.profile.save',
    'uses' => 'UserController@profileSave'
]);

Route::get('user/options', [
    'as' => 'user.options',
    'uses' => 'UserController@options'
]);

Route::post('user/options', [
    'as' => 'user.options.save',
    'uses' => 'UserController@optionsSave'
]);

Route::get('lang/{lang}', [
    'as' => 'switch.language',
    'uses' => 'LanguageController@switchTo'
]);

Route::get('json/users/names', [
    'as' => 'json.users/names',
    'uses' => 'JsonRestController@allUserNames'
]);



Route::controllers([
	'auth' => 'Auth\AuthController',
	'password' => 'Auth\PasswordController',
]);
