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
			->with('notice', "You've successfully logged in as $user->username");
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

		if(Auth::attempt(array('username' => $userdata['username'], 'password' => $userdata['password']), isset($userdata['remember']) ? true : false)) {
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
		sifntUserAuth::logout();

		return Redirect::route('home')
			->with('notice', 'You successfully logged off');
	}

	public function getLoginWithCas()
	{
		Auth::logout();

		if(!phpCAS::isAuthenticated()) {
			phpCAS::forceAuthentication();
		}
		else {
			$userauth = new sifntUserAuth();
			if($user_id = $userauth->getUserId($userauth->getUserName())){
				Auth::loginUsingId($user_id);
			}			

			return Redirect::route('home')
				->with('notice', "You've successfully logged in as $user->username");
		}
	}

	public function getForgotPassword()
	{
		return View::make('users/forgot-password');
	}


	public function postForgotPassword()
	{
		$formdata = Input::all();

		// Try for user with matching username and email
		if($user = User::where('username', '=', $formdata['username'])
		->where('email', '=', $formdata['email'])
		->first()) {
			if(Password::remind(Input::only('email'), function($message)
				{
					$message->subject('Password Reminder');
				}
			)) {
				return Redirect::route('home')
					->with('notice', 'A password reset email has been sent to your specified email address, please follow the instructions in this email to complete your password reset.');
			}
			else {
				return Redirect::to(URL::previous())
					->with('error', 'An error occured sending a reminder email.')
					->withInput($formdata);
			}
		}
		// Check for username with no password or email address
		else if($user = User::where('username', '=', $formdata['username'])
		->where('email', '=', '')
		->where('password', '=', '')
		->first()) {
			return Redirect::to(URL::to('users/register'))
				->with('notice', 'This user already exists but has no registered Email Address.  You may claim it by registering it now.')
				->withInput($formdata);
		}
		else {
			return Redirect::to(URL::previous())
				->with('error', 'Username and email address do not match or Username not registered.  Note: These fields are case-sensitive.')
				->withInput($formdata);
		}
	}
}