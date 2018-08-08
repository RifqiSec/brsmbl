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

$router->get('/', function () use ($router) {
	return $router->app->version();
});
$router->post('auth/login','AuthController@authenticate');
$router->post('auth/register','AuthController@register');

$router->get('vehicle','VehicleController@index');
$router->get('search','SearchController@index');
$router->get('dealer','DealerController@index');
$router->get('dealer/{id}','DealerController@show');

$router->group(['middleware' => 'jwt.auth'], function() use ($router) {

	$router->post('auth/otp','AuthController@validateOTP');
	$router->get('profile','UserController@profile');
	$router->get('user','UserController@index');
	$router->get('user/{id}','UserController@show');
	$router->post('user/{id}','UserController@update');
	$router->delete('user/{id}','UserController@destroy');


	$router->get('inbox','InboxController@index');
	$router->post('inbox','InboxController@create');
	$router->get('transaction','TransactionController@index');

});