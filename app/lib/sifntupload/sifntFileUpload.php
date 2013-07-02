<?php
class sifntFileUpload {
	private $path_dest;
	private $path_cache;
	private $path_magicdb;

	function __construct($path_dest = null){
		$this->path_dest = $path_dest ? $path_dest : public_path() . "/files";
		$this->path_cache = $this->path_dest . '/cache';
		$path_magicdb = null; // FIXME
	}

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

	public function processFile (&$uploadData)
	{
		$error = false;

		$file_path = $this->path_dest . '/' . $uploadData['file_datename'] . '.' . $uploadData['file_ext'];
		
		if($uploadData['copy_mode'] == 'copy'){
			$filemove = copy($uploadData['tmp_name'], $file_path);
		}
		else{
			$filemove = rename($uploadData['tmp_name'], $file_path);
		}

		if($filemove){
			// Check if file needs extra processing, if it does do it now
			switch($uploadData['file_extra']){
				case 'image':
					if(!$this->processimage($file))
						return false;
						//$error['critical'][] = 'Image processing failed.';
					
					break;
				
				case 'document':
					if(!$this->processdocument($file))
						return false;
						//$error['critical'][] = 'Document processing failed.';
						
					break;
				
				case 'video':
					if(!$this->processvideo($file))
						return false;
						//$error['critical'][] = 'Video processing failed.';
						
					break;
				
				case 'audio':
					if(!$this->processaudio($file))
						return false;
						//$error['critical'][] = 'Audio processing failed.';
						
					break;
			}
			
			if(!$error){
				$this->addtodb($uploadData);
				return true;
			}
		}
		
		// If any errors occur, back out all changes
		if($error){
			$this->deletefilebyfilepath($file_path);
			return false;
		}

		return array('success' => true);
	}

	// Get all file data as an array
	public function getUploadData($path_src, $file_src, $postData)
	{
		$file_source = 'file'; // Hardcoded for now, URL support will come later
		$uploadData = [];
		$user_id = Auth::user()->id;

		switch($file_source){
			case 'file':
				// File Name
				$uploadData['tmp_name'] = $path_src . '/' . $file_src;

				if($postData['qqfile']['name'] != 'blob'){
					$uploadData['file_fullname'] = $postData['qqfile']['name'];
				}
				else{
					$uploadData['file_fullname'] = $postData['qqblobname'];
				}

				$uploadData['copy_mode'] = 'move';
				
				break;
		}
					
		// Physical File Attributes
		$uploadData['file_type'] = $this->getfilemime($uploadData['tmp_name']);
		$uploadData['file_size'] = filesize($uploadData['tmp_name']);
		
		// File Name Stuff
		$namesplit = $this->namesplit($this->path_dest . '/' . $uploadData['file_fullname'], $uploadData['file_type']);
		$uploadData['file_name'] = addslashes($namesplit[2]);
		$uploadData['file_ext'] = strtolower($namesplit[3]);
		$uploadData['file_fullname'] = $uploadData['file_name'] . "." . $uploadData['file_ext'];
		$uploadData['file_description'] = $uploadData['file_name']; // TODO
		$uploadData['file_datename'] = $this->datename($uploadData['file_description']);
		$uploadData['file_cleanname'] = $this->cleanname($uploadData['file_description'], true, $user_id);
		
		// File Custom Attributes
//		if(!$uploadData['filegroup_id'] = $formdata['filegroup_id']) TODO
//			$error['validation'][] = 'Please select a Group';
//		$uploadData['file_tags'] = $formdata['file_tags']; TODO
		
		// Image Specific Options
		if($uploadData['image_info'] = getimagesize($uploadData['tmp_name'])){
			$uploadData['file_extra'] = 'image';
			//$uploadData['image_rotate'] = $formdata['image_rotate']; TODO
			$uploadData['image_width'] = $uploadData['image_info'][0];
			$uploadData['image_height'] = $uploadData['image_info'][1];
			$uploadData['image_bits'] = $uploadData['image_info']['bits'];
			$uploadData['image_channels'] = empty($uploadData['image_info']['channels']) ? 1 : $uploadData['image_info']['channels'];
			$uploadData['image_type'] = $uploadData['image_info']['mime'];
		}
		
		// Document Specific Options
		elseif($this->isDocument($this->path_dest . '/' . $uploadData['file_fullname'])){
			$uploadData['file_extra'] = 'document';
			$uploadData['document_pdf'] = $formdata['document_pdf'];
		}
		
		// Video Specific Options
		elseif($this->isVideo($this->path_dest . '/' . $uploadData['file_fullname'])){
			$uploadData['file_extra'] = 'video';
		}
		
		// Audio Specific Options
		elseif($this->isAudio($this->path_dest . '/' . $uploadData['file_fullname'])){
			$uploadData['file_extra'] = 'audio';
		}

		else{
			$uploadData['file_extra'] = false;
		}

		return $uploadData;
	}

	public static function namesplit($path_file, $file_type = null)
	{
		// Full filepath and name
		$namesplit[] = $path_file;
		
		// Get path only
		if(false !== strpos($path_file, '/')){
			$last_slash = strrpos($path_file, '/') + 1;
			$namesplit[] = substr($path_file, 0, $last_slash);
		}
		else{
			$last_slash = 0;
			$namesplit[] = '';
		}
		
		// Get filename with extension
		$file_name = substr($path_file, $last_slash);
		
		// Check for extension
		if($last_dot = strrpos($file_name, '.')){
			$namesplit[] = substr($file_name, 0, $last_dot);
			$namesplit[] = substr($file_name, $last_dot + 1);
		}
		
		// No extension, we need to assign one TODO
		/*else{
			if(!$filetype){
				$error[]['critical'] = 'Source file has no extension and no filetype data was provided';
				return false;
			}
			
			$namesplit[] = $filename;
			
			$sql = "select fileext_ext from ".$db_tp."fileextensions where fileext_mime = '$filetype'";
			$result = $db->queryOne($sql);
			if(checkdberror($result)){
				if($result){
					$namesplit[] = $result;
				}
				else{
					$error['critical'] = "Could not determine a file extension based on file type '$filetype'";
				}
			}
		}*/

		return $namesplit;
	}

	public function isDocument($path_file) // TODO
	{
		return false;
	}

	public function isVideo($path_file) // TODO
	{
		return false;
	}

	public function isAudio($path_file) // TODO
	{
		return false;
	}

	// Get file mime type
	function getfilemime($path_file){
		
		$finfo = finfo_open(FILEINFO_MIME, $this->path_magicdb);
		$file_mime = finfo_file($finfo, $path_file);
		finfo_close($finfo);
		
		if(preg_match("/^[a-z0-9.\-]+\/[a-z0-9.\-]+/", $file_mime, $filemimeparts)){
			$file_mime = $filemimeparts[0];
		}
		
		return $file_mime;
	}

	// Generate Date-Based Filename
	public function datename($text, $date = null){
		if(!$date){
			$date = time();
		}
		
		// Append Date and Time
		$name = $this->cleanname($text) . date("_Ymd-his", $date);

		// Append random number to avoid overlaps
		$name = $name . "-" . str_pad(mt_rand(0,9999), 4, '0', STR_PAD_LEFT);
		
		return $name;
	}

	// Generate 'Clean' Filename
	public function cleanname($text, $nodups = false, $user_id = null){
		$name = strtolower($text); // Convert text to lowercase
		$name = preg_replace('/[^a-z0-9\_]/i', '_', $name); // Non-valid characters become _
		$name = preg_replace('/\_[\_]*/i', '_', $name); // Remove multiple _
		$name = preg_replace('/(^\_)|(\_$)/', '', $name); // Remove beginning and ending _
		
		if($nodups){
			$user_id = $user_id ? $user_id : Auth::user()->id;

			$upload = Upload::whereRaw("user_id = $user_id and cleanname like '$name%'")->orderBy('created_at', 'desc')->first();
			if($upload){
				$file_cleanname = $upload->cleanname;

				if(preg_match("/_([0-9]+)$/", $file_cleanname, $arr)){
					$arr[1]++;
					$name .= '_' . $arr[1];
				}
				else{
					$name .= '_2';
				}				
			}
		}

		return $name;
	}

	public  function deletefile($id){
		$upload = Upload::find($id);
		if($upload){
			$file_path = public_path() . '/files/' . "$upload->name.$upload->ext";
			$this->deletefilebyfilepath($file_path);
		}

		return true;
	}

	// Delete all file data (Physical and DB) by the filepath
	public  function deletefilebyfilepath($file_path){
		// Check if file exists
		if(!is_file($file_path)){
			return false;
		}
		
		// Check for a file_id
		$namesplit = $this->namesplit($file_path);
		$file_name = $namesplit['2'];
		
		$upload = Upload::where('name', '=', $file_name)->first();
		$upload_id = $upload->id;

		if($upload->delete()){
			// Delete database references
			Image::where('upload_id', '=', $upload_id)->delete();

			// Delete file itself
			if(unlink($file_path)){
				// Delete cached files based on this one
				$dir = opendir($this->path_cache);
				while(false !== ($cachedfile = readdir($dir))){
					if(0 === strpos($cachedfile, $file_name)){
						unlink($this->path_cache . '/' . $cachedfile);
					}
				}

				return true;
			}		
		}

		return false;
	}

	public function processimage(&$uploadData) // TODO
	{
		return true;
	}

	public function processvideo(&$uploadData) // TODO
	{
		return true;
	}

	public function processdocument(&$uploadData) // TODO
	{
		return true;
	}

	public function processaudio(&$uploadData) // TODO
	{
		return true;
	}
			
	// Add file to DB
	private function addtodb(&$file){
		// Insert into DB
		if($this->addfiletodb($file)){
			// Process tags
			//addtagstodb($file); TODO
			
			// Process extras
			switch($file['file_extra']){
				case 'image':
					$this->addimagetodb($file);
					break;

				case 'video':
					$this->addvideotodb($file);
					break;

				case 'audio':
					$this->addaudiotodb($file);
					break;
			}

			return true;
		}
	}

	// Add file itself to database; returns $file_id
	private function addfiletodb(&$file){
		$upload = new Upload;

		$upload->user_id = Auth::user()->id;
		//$upload->filegroup_id = $file['filegroup_id']; // TODO
		$upload->description = $file['file_description'];
		$upload->name = $file['file_datename'];
		$upload->cleanname = $file['file_cleanname'];
		$upload->originalname = $file['file_name'];
		$upload->ext = $file['file_ext'];
		$upload->size = $file['file_size'];
		$upload->type = $file['file_type'];
		$upload->extra = $file['file_extra'];

		$upload->save();

		if($file_id = $upload->id){
			$file['file_id'] = $file_id;

			return true;
		}
		
		return false;
	}

	// Add image info to database
	private function addimagetodb($file){
		$image = new Image;

		$upload = Upload::find($file['file_id']);

		$image->type = $file['image_type'];
		$image->width = $file['image_width'];
		$image->height = $file['image_height'];
		$image->bits = $file['image_bits'];
		$image->channels = $file['image_channels'];

		if($image = $upload->image()->save($image)){
			return true;
		}
		
		return false;
	}


	private function addvideotodb($file){
		return true;
	}

	private function addaudiotodb($file){
		return true;
	}

	// Deleted cache for particular file
	public function deletefilecache($id){
		$upload = Upload::find($id);

		// Delete cached files based on this one
		$dir = opendir($this->path_cache);
		while(false !== ($cachedfile = readdir($dir))){
			if(0 === strpos($cachedfile, $upload->name)){
				unlink($this->path_cache . '/' . $cachedfile);
			}
		}
	}
}