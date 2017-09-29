<?php
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
// Home page
$app->get('/', function () use ($app) {
    return $app->version();
});

// Users
$app->post('/user/save', 'UserController@store');
$app->post('/user/updateProfile', 'UserController@update');
$app->post('/user/get-by-id', 'UserController@show');

// contact
$app->post('/contact/import-contacts','ContactController@store');
$app->post('/contact/update-user-number','ContactController@update');

// Request an access token
$app->post('/oauth/access_token', function() use ($app){
    return response()->json($app->make('oauth2-server.authorizer')->issueAccessToken());
});