<?php
/**
* 
*/
class sifntFileUtil
{
	public static function cleantext($text){
		$text = strtolower($text); // Convert text to lowercase
		$text = preg_replace('/[^a-z0-9\_]/i', '_', $text); // Non-valid characters become _
		$text = preg_replace('/\_[\_]*/i', '_', $text); // Remove multiple _
		$text = preg_replace('/(^\_)|(\_$)/', '', $text); // Remove beginning and ending _
		
		return $text;
	}
}