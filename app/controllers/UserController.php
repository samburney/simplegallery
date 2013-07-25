<?php
class UserController extends BaseController
{
	public function __contruct()
	{
		Auth::logout();
	}

	public function getRegister()
	{
		return View::make('users/register');
	}

	public function postRegister()
	{
		Validator::extend('unique_username', function($attribute, $value, $parameters)
		{
			if($user = User::where('username', '=', $value)->first()){
				if(!$user->password && !$user->email){
					return true;
				}
				else{
					return false;
				}
			}
			else{
				return true;
			}
		});

		Validator::extend('unique_email', function($attribute, $value, $parameters)
		{
			if($user = User::where('email', '=', $value)->first()){
				return false;
			}
			else{
				return true;
			}
		});

		$rules = array(
			'username' => 'required|min:2|max:32|alpha_num|unique_username',
			'email' => 'required|email|unique_email',
			'password' => 'required|min:6',
			'password_confirm' => 'same:password',
		);
		$validator = Validator::make(Input::all(), $rules);

		if($validator->fails()) {
			return Redirect::to(URL::previous())
				->withErrors($validator)
				->withInput(Input::except(array('password', 'password_confirm')));
		}

		$userdata = Input::except('password_confirm');
		$userdata['password'] = Hash::make($userdata['password']);

		// Check if user exists
		if($user = User::where('username', '=', $userdata['username'])->first()){
			$user->update($userdata);
		}
		else{
			$user = User::create($userdata);
		}

		$user->save();

		Auth::loginUsingId($user->id);
		return Redirect::route('home')
			->with('notice', "Welcome to sifntUpload, $user->username"); //FIXME, should be dynamic sitename
	}

	public function getLogin()
	{
		return View::make('users/login');
	}

	public function postLogin()
	{
		$userdata = Input::all();

		// Check for email address
		if($user = User::where('email', '=', $userdata['username'])->first()){
			$userdata['username'] = $user->username;
		}

		if(Auth::attempt($userdata)){
			return Redirect::intended(URL::to('uploads'))
				->with('notice', 'Welcome back, ' . Auth::user()->username);
		}
		else{
			return Redirect::to(URL::previous())
				->with('error', 'Incorrect User name, Email address or Password')
				->withInput(Input::except('password'));
		}
	}

	public function getLogout()
	{
		Auth::logout();
		return Redirect::route('home')
			->with('notice', 'You successfully logged off');
	}
}