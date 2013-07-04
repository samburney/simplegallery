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
}