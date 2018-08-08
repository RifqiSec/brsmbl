<?php

use Illuminate\Http\Request;


$router->get('/', function () use ($router) {
	return $router->app->version();
});
$router->post('auth/login','AuthController@authenticate');
$router->post('auth/register','AuthController@register');

$router->get('vehicle','VehicleController@index');
$router->get('search','SearchController@index');
$router->get('dealer','DealerController@index');

$router->get('city', function(Request $request) {
	return [
		'status' => 'success',
		'data' => App\City::select('id', 'name')->where('name', 'like', '%'.$request->get('param').'%')->get()
	];
});

$router->get('brand', function(Request $request) {
	return [
		'status' => 'success',
		'data' => App\VehicleBrand::select('id', 'name')->where('name', 'like', '%'.$request->get('param').'%')->get()
	];
});


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