<?php
/**
* 
*/
class Collection extends Eloquent
{
	public function uploads()
	{
		return $this->belongsToMany('Upload');
	}

	public function user()
	{
		return $this->belongsTo('User');
	}
}