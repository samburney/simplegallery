<?php
/**
* 
*/
class Tag extends Eloquent
{
	
	public function uploads()
	{
		return $this->belongsToMany('Upload');
	}
	
	public function public_uploads()
	{
		return $this->belongsToMany('Upload')
					->where('private', '=', 0)
					->orWhere('user_id', '=', Auth::user()->id);
	}
}