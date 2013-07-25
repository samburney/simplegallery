<?php

class BaseController extends Controller {
	protected $layout = 'layouts.main';
	protected $user;

	public function __construct()
	{
		// Autologin if possible
		if(!Auth::check()){
			$userauth = new sifntUserAuth();
			if($user_id = $userauth->getUserId($userauth->getUserName())){
				Auth::loginUsingId($user_id);
			}
			
			//if(Auth::check()){  // FIXME, I don't like this showing up two requests in a row
			//	Session::flash('warning', "You've been automatically logged in as " . Auth::user()->username . '. <a href="#">Why?</a>');
			//}
		}

		$this->user = Auth::user();
	}

	/**
	 * Setup the layout used by the controller.
	 *
	 * @return void
	 */
	protected function setupLayout()
	{
		if ( ! is_null($this->layout))
		{
			$this->layout = View::make($this->layout);
		}
	}

}