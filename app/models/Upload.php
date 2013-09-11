<?php
class Upload extends Eloquent
{
	protected $table = 'uploads';

	public function image()
	{
		return $this->hasOne('Image');
	}

	public function collections()
	{
		return $this->belongsToMany('Collection');
	}

	public function tags()
	{
		return $this->belongsToMany('Tag');
	}

	public function scopePopular($query)
	{
		return $query->where('viewed', '>', 10)->where('private', '!=', '1');
	}
}
?>