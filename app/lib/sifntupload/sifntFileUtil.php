<?php
/**
* 
*/
class sifntFileUtil
{
	public static function cleantext($text, $table = null){
		$text = strtolower($text); // Convert text to lowercase
		$text = preg_replace('/[^a-z0-9\_]/i', '_', $text); // Non-valid characters become _
		$text = preg_replace('/\_[\_]*/i', '_', $text); // Remove multiple _
		$text = preg_replace('/(^\_)|(\_$)/', '', $text); // Remove beginning and ending _
		
		if($table){
			if($name_existing = DB::table($table)->where('name_unique', 'LIKE', "$text%")->orderBy('created_at', 'desc')->first()) {
				if(preg_match("/_([0-9]+)$/", $name_existing->name_unique, $arr)){
					$arr[1]++;
					$text .= '_' . $arr[1];
				}
				else{
					$text .= '_2';
				}				
			}			
		}

		return $text;
	}

	public static function createZip($name, $ids) {
		$zip = new ArchiveStream_zip("$name.zip");

		foreach($ids as $upload) {
			$zip->add_file_from_path($name . '/' . $upload['originalname'] . '.' . $upload['ext'], public_path() . '/files/' . $upload['name'] . '.' . $upload['ext']);
		}

		$zip->finish();

		exit();
	}
}