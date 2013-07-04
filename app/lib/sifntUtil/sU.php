<?php
/**
* 
*/
class sU
{
	// Quick function to call print_r within a HTML <pre>
	public static function debug($arr){
		$backtrace = debug_backtrace();
		$file = $backtrace[0]['file'];
		$line = $backtrace[0]['line'];
		
		echo '<pre>';
		echo "<b>$file:$line</b>\n";
		print_r($arr);
		echo '</pre>';
	}
}