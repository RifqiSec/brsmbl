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

$router->get('dealer', function(Request $request) {
	return [
		'status' => 'success',
		'data' => App\Dealer::select('id', 'name', 'city_id')
		->with('city')
		->where('name', 'like', '%'.$request->get('\\param').'%')
		->get()
		->map(function($item) {
			return [
				'id' => $item->id,
				'name' => $item->name,
				'area' => $item->city->name,
			];
		})
	];
});

$router->get('sales/{area}/{vehicle_id}','TransactionController@salesList');


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
	$router->post('sales/select','TransactionController@selectSales');


	$router->get('sales/pending','DealerController@salesPending');
	$router->post('sales/approve','DealerController@salesApprove');


	$router->get('token/history','TokenController@index');
	$router->get('payment/history','PaymentController@index');


	$router->get('dealer/vehicle','DealerController@vehicleList');


});