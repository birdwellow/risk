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



/*
 * 
 * Route Complex: User
 * 
 */

Route::get('/', [
    'as' => 'index',
    'uses' => 'AccountController@index'
]);

Route::get('options', [
    'as' => 'user.options',
    'uses' => 'AccountController@options'
]);

Route::post('options', [
    'as' => 'user.options.save',
    'uses' => 'AccountController@optionsSave'
]);

Route::post('changepassword', [
    'as' => 'user.password.save',
    'uses' => 'AccountController@passwordSave'
]);

Route::get('switchlanguage/{lang}', [
    'as' => 'switch.language',
    'uses' => 'AccountController@switchToLanguage'
]);

Route::get('json/users/names', [
    'as' => 'json.users/names',
    'uses' => 'JsonRestController@allUserNamesExceptCurrentUser'
]);




/*
 * 
 * Route Complex: Match
 * 
 */

Route::get('match/new', [
    'as' => 'match.new',
    'uses' => 'MatchController@createMatchForm'
]);

Route::post('match/new', [
    'as' => 'match.new.create',
    'uses' => 'MatchController@createMatch'
]);

Route::get('match', [
    'as' => 'match.goto',
    'uses' => 'MatchController@goToMatch'
]);

Route::get('match/join/{id}', [
    'as' => 'match.join.init',
    'uses' => 'MatchController@joinMatchForm'
]);

Route::post('match/join/{id}', [
    'as' => 'match.join.confirm',
    'uses' => 'MatchController@joinMatch'
]);

Route::get('match/cancel', [
    'as' => 'match.cancel',
    'uses' => 'MatchController@cancelMatch'
]);

Route::get('match/administrate', [
    'as' => 'match.administrate',
    'uses' => 'MatchController@administrateMatchForm'
]);

Route::post('match/administrate', [
    'as' => 'match.administrate.save',
    'uses' => 'MatchController@administrateMatchSave'
]);

Route::post('match/inviteusers', [
    'as' => 'match.administrate.inviteusers',
    'uses' => 'MatchController@inviteUsers'
]);

Route::get('match/search', [
    'as' => 'match.search',
    'uses' => 'MatchController@searchMatch'
]);



/*
 * 
 * Route Complex: Messages
 * 
 */

Route::get('thread/new', [
    'as' => 'new.thread.init',
    'uses' => 'MessageController@newThreadWithNewMessageForm'
]);

Route::post('thread/new', [
    'as' => 'new.thread.create',
    'uses' => 'MessageController@createNewThreadWithNewMessage'
]);

Route::post('thread/{threadId}/newmessage', [
    'as' => 'thread.newmessage',
    'uses' => 'MessageController@newMessageInThread'
]);

Route::get('threads', [
    'as' => 'all.threads',
    'uses' => 'MessageController@showAllThreads'
]);

Route::get('thread/{threadId}', [
    'as' => 'thread.allmessages',
    'uses' => 'MessageController@showThread'
]);

Route::post('thread/{threadId}/addusers', [
    'as' => 'thread.addusers',
    'uses' => 'MessageController@addUsers'
]);

Route::get('thread/{threadId}/ajaxpart', [
    'as' => 'ajax.thread.part',
    'uses' => 'MessageController@loadThreadPart'
]);



Route::get('auth/login', [
    'as' => 'show.login',
    'uses' => 'AuthController@showLogin'
]);

Route::post('auth/login', [
    'as' => 'login',
    'uses' => 'AuthController@login'
]);

Route::get('auth/register', [
    'as' => 'show.register',
    'uses' => 'AuthController@showRegister'
]);

Route::post('auth/register', [
    'as' => 'register',
    'uses' => 'AuthController@register'
]);

Route::get('auth/logout', [
    'as' => 'logout',
    'uses' => 'AuthController@logout'
]);



Route::get('password/email', [
    'as' => 'passwordreset.emailform',
    'uses' => 'PasswordController@getEmail'
]);

Route::post('password/email', [
    'as' => 'passwordreset.sendemail',
    'uses' => 'PasswordController@postEmail'
]);

Route::get('password/reset/{token}', [
    'as' => 'passwordreset.confirm',
    'uses' => 'PasswordController@getReset'
]);

Route::post('password/reset', [
    'as' => 'passwordreset.perform',
    'uses' => 'PasswordController@postReset'
]);




Route::get('test', [
    'as' => 'test.route',
    'uses' => 'TestController@perform'
]);