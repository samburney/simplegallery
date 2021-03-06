<?php

/*
|--------------------------------------------------------------------------
| Application & Route Filters
|--------------------------------------------------------------------------
|
| Below you will find the "before" and "after" events for the application
| which may be used to do any work before or after a request into your
| application. Here you may also register your custom route filters.
|
*/

App::before(function($request)
{
	//
});


App::after(function($request, $response)
{
	//
});

/*
|--------------------------------------------------------------------------
| Authentication Filters
|--------------------------------------------------------------------------
|
| The following filters are used to verify that the user of the current
| session is logged into this application. The "basic" filter easily
| integrates HTTP Basic authentication for quick, simple checking.
|
*/

Route::filter('auth', function()
{
	if (Auth::guest()) return Redirect::guest('login');
});


Route::filter('auth.basic', function()
{
	return Auth::basic();
});

/*
|--------------------------------------------------------------------------
| Guest Filter
|--------------------------------------------------------------------------
|
| The "guest" filter is the counterpart of the authentication filters as
| it simply checks that the current user is not logged in. A redirect
| response will be issued if they are, which you may freely change.
|
*/

Route::filter('guest', function()
{
	if (Auth::check()) return Redirect::to('/');
});

/*
|--------------------------------------------------------------------------
| CSRF Protection Filter
|--------------------------------------------------------------------------
|
| The CSRF filter is responsible for protecting your application against
| cross-site request forgery attacks. If this special token in a user
| session does not match the one given in this request, we'll bail.
|
*/

Route::filter('csrf', function()
{
	if (Session::token() != Input::get('_token'))
	{
		throw new Illuminate\Session\TokenMismatchException;
	}
});

// Define the $user variable for most views
View::composer(array(
		'layouts.main',
	),
	function($view)
	{
		$view->with('user', Auth::user());
	}
);

// Define the $collection_list variable for most view with upload-sidebar
View::composer(array(
		'includes.upload-sidebar',
	),
	function($view)
	{
		$collection_list = Auth::user()->collections()->with('uploads.image')->orderBy('name_unique')->get();

		$view->with('collection_list', $collection_list);
	}
);

function baseURL()
{
	return URL::to('');
}

if(Config::get('auth.cas')) {
		phpCAS::client(
			CAS_VERSION_2_0,
			Config::get('auth.cas.host'),
			Config::get('auth.cas.port'),
			Config::get('auth.cas.context')
		);

		phpCAS::setCasServerCACert(Config::get('auth.cas.cacert'));
}