<?php
class Upload extends Eloquent
{
	protected $table = 'uploads';

	public function image()
	{
		return $this->hasOne('Image');
	}
}
?>