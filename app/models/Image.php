<?php
class Image extends Eloquent
{
	protected $table = 'images';

	public function upload()
	{
		return $this->belongsTo('Upload');
	}
}
?>