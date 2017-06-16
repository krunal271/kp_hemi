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

// Route::get('/', function () {
//     return view('home');
// });

Route::auth();



// Route::get('/home', 'HomeController@index');
Route::group(['namespace'=>'frontend'],function()
{	
	Route::get('/','HomeController@index');
	Route::get('/portfolio','HomeController@portfolio');

	Route::post('/contact','HomeController@contact');
});


Route::group(['prefix' => 'admin', 'namespace' => 'Admin'], function()
{	
	Route::get('/', 'SliderController@index');
	Route::get('/home', 'SliderController@index');

	Route::get('/dashboard', 'DashboardController@index');
	Route::get('/profile/{id}/edit', 'UsersController@profile');
	Route::post('/profile/{id}/update', 'UsersController@update');
	
	Route::resource('/menu', 'MenuController');
	Route::resource('/slider', 'SliderController');
	Route::resource('/aboutus', 'AboutusController');
	Route::resource('/media', 'MediaController');
	Route::resource('/services', 'ServicesController');	
	Route::resource('/gallery', 'GalleryController');
	Route::resource('/blogs', 'BlogsController');
	Route::resource('/locations', 'LocationsController');
	Route::resource('/arts', 'ArtsController');
	Route::resource('/users', 'UsersController');
	Route::resource('/video', 'VideoController');	

	Route::post('/services/updateorder/{id}', 'ServicesController@updateorder');
	Route::post('/slider/updateorder/{id}', 'SliderController@updateorder');
	Route::post('/blogs/updateorder/{id}', 'BlogsController@updateorder');
	Route::post('/locations/updateorder/{id}', 'LocationsController@updateorder');
	Route::post('/arts/updateorder/{id}', 'ArtsController@updateorder');
	Route::post('/users/updateorder/{id}', 'UsersController@updateorder');
	Route::post('/gallery/updateorder/{id}', 'GalleryController@updateorder');
	Route::post('/media/updateorder/{id}', 'MediaController@updateorder');	
	Route::post('/video/updateorder/{id}', 'VideoController@updateorder');		
});
