<?php
class sifntFileConvert {

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

	// Convert file to another file type (Wrapper)
	public function convertfile(&$file, $target_ext){
	    // Get requested type (To handle on-the-fly file conversions) TODO
	    $target_type = 'image'; // Hardcoded - FIXME
	    /*$sql = "select fileext_type from ".$db_tp."fileextensions where fileext_ext = '$target_ext'";
	    $result = $db->queryOne($sql);
	    if(checkdberror($result)){
	        if(!$target_type = $result){
	            $error['critical'][] = 'A file conversion has been requested but the target filetype is unknown.';
	        }
	    }*/

	    switch($target_type){
	        case 'image':
	            return $this->convertfile2image($file, $target_ext);
	            break;
	        
	        default:
	            break;
	    }
	    
	    return false;
	}

	public function convertfile2image(&$file, $target_ext){
	    $file_path = public_path() . '/files/' . $file['name'] . '.' . $file['ext'];
	    $new_filename = 'cache/' . $file['name'];
	    $new_filepath = public_path() . '/files/cache/' . $file['name'] . "." . $target_ext;
	    
	    // Skip conversion if file already exists in cache
	    if(!is_file($new_filepath)){
	        switch($file['extra']){
	            // Images are transcoded directly to new image type
	            case 'image':
	                $image = $this->imagecreatefrommime($file_path);
	                
	                break;
				
				/*case 'video': TODO
					$movie = new ffmpeg_movie($file_path);
					
					$frame_num = ($file['video_duration'] * $file['video_fps'])/10;
					$movie->getFrame($frame_num);
					$frame = $movie->getNextKeyFrame();
					
					$image = $frame->toGDImage();
					
					break;*/
	            
	            // Everything else is assigned a file-type based icon
	            default:
	                // Check for icon file
	                if(is_file(public_path() . '/icons/' . $file['ext'] . '.gif')){
	                    $file_path = public_path() . '/icons/' . $file['ext'] . '.gif';
	                    $image = $this->imagecreatefrommime($file_path);
	                }

	                // Generate filetype-icon where none exists
	                else{
	                    $image = $this->imagecreatefrommime(public_path() . '/icons/icon-template.gif');
	                    
	                    $text = '.' . $file['ext'];
	                    $font = public_path() . '/fonts/misterearlbt.ttf';
	                    $font_size = 18;
	                    $text_color = imagecolorallocate($image, 0x00, 0x48, 0x9c);
	                    
	                    $text_box = imageftbbox($font_size, 0, $font, $text);
	                    $text_width = $text_box[2] - $text_box[0];
	                    $text_height = $text_box[3] - $text_box[1];
	                    imagefttext($image, $font_size, 0, 44 - $text_width, 45 - $text_height, $text_color, $font, $text);
	                }

	                break;
	        }
	        
	        // Create output image
	        switch($target_ext){
	            case 'jpeg':
	            case 'jpg':
	            case 'jpe':
	                if($file['ext'] == 'jpe' || $file['ext'] == 'jpg' || $file['ext'] == 'jpeg')
	                    return true;

	                imagejpeg($image, $new_filepath);
	                break;
	            
	            case 'gif':
	                imagegif($image, $new_filepath);
	                break;
	                
	            case 'png':
	                imagepng($image, $new_filepath);
	                break;
	            
	            default:
	                $error['critical'][] = "$target_ext is not a supported target image type";
	                return false;
	                break;
	        }
	    }

	    // Update $file array
	    $image_info = getimagesize($new_filepath);

	    $file['name'] = $new_filename;
	    $file['ext'] = $target_ext;
	    $file['size'] = filesize($new_filepath);
	    $file['extra'] = 'image';
	    $file['type'] = $image_info['mime'];
	    $file['image_type'] = $image_info['mime'];
	    $file['image_width'] = $image_info[0];
	    $file['image_height'] = $image_info[1];

	    return true;
	}

	// Create blank transparent PNG
	public function imagecreatetransparent($width, $height){
		$image=imagecreatetruecolor($width,$height);
		imagealphablending($image,false);
		$col=imagecolorallocatealpha($image,255,255,255,127);
		imagefill($image, 0, 0, $col);
		imagealphablending($image,true);
		
		return $image;
	}

	// Create image based on mimetype
	public function imagecreatefrommime($filepath){
		$imageinfo = getimagesize($filepath);
		
		switch ($imageinfo['mime']) {
			case 'image/jpeg':
				return imagecreatefromjpeg($filepath);
				break;
			
			case 'image/gif':
				return imagecreatefromgif($filepath);
				break;
			
			case 'image/png':
				$image = imagecreatefrompng($filepath);
				imagesavealpha($image, true);
				return $image;
				break;
		}
		
		return false;
	}

	// Save image based on mimetype
	public function saveimageasmime($image, $filepath, $mime = 'image/jpeg', $compression = false){
		switch($mime){
			case 'image/jpeg':
				if(!$compression) $compression = 80;
				return imagejpeg($image, $filepath, $compression);
				break;

			case 'image/gif':
				return imagegif($image, $filepath);
				break;

			case 'image/png':
				if(!$compression) $compression = 9;
				return imagepng($image, $filepath, $compression);
				break;
		}
		
		return false;
	}


	// Resize an image to a maximum size and include a watermark/logo
	public function resizeimage($filepath, $newsize, $append = false, $allowenlarge = 0, $keepaspectratio = 1, $attachwm = true, $logo = false, $outputformat = 'jpg'){
		// Get requested new size values
		switch($newsize){
			case 'avatar':
				$maxwidth = '100';
				$maxheight = '100';
				$attachwm = false;
				break;

			case 'thumb':
				$maxwidth = '200';
				$maxheight = '200';
				$attachwm = false;
				break;
			
			case 'small':
				$maxwidth = '800';
				$maxheight = '600';
				break;
			
			case 'large':
				$maxwidth = '1280';
				$maxheight = '1024';
				break;
			
			default:
				$dims = explode('x', $newsize);
				if(sizeof($dims) == 1){
					$maxwidth = $dims[0];
					$maxheight = $dims[0];
				}
				else if(sizeof($dims) == 2){
					$maxwidth = $dims[0];
					$maxheight = $dims[1];
				}
				else{
					header('HTTP/1.0 404 Not Found');
					die('New size must be a keyword or in the form <width>x<height>');
				}
				
				if(!is_numeric($maxwidth) || !is_numeric($maxheight)){
					header('HTTP/1.0 404 Not Found');
					die('New size must be a keyword or in the form &lt;width&gt;x&lt;height&gt;');
				}
				
				if($maxwidth <= '200' && $maxheight <= '200'){
					$attachwm = false;
				}
				
				break;
		}
		
		// Determine /actual/ new size values based on the options provided
		// Get real filesize
		$imageinfo = getimagesize($filepath);
		$width = $imageinfo[0];
		$height = $imageinfo[1];

		// Determine aspect ratio
		if($keepaspectratio){
			$aspectratio = $width / $height;
		}
		else{
			$aspectratio = $maxwidth / $maxheight;
		}
		
		// If image is the same or smaller than new size and enlarging is disabled, return current image
		if($width <= $maxwidth && $height <= $maxheight && !$allowenlarge){
			return $filepath;
		}
		
		// Determine new sizes
		if($maxwidth / $aspectratio <= $maxheight){
			$newwidth = $maxwidth;
			$newheight = round($maxwidth / $aspectratio);
		}
		else{
			$newheight = $maxheight;
			$newwidth = round($newheight * $aspectratio);
		}
		
		// Work out paths
		$namesplit = sifntFileUpload::namesplit($filepath);
		$cacheident = '-'.$newwidth.'x'.$newheight;
		$newfilepath = public_path() . '/files/cache/' . $namesplit[2] . "$cacheident.jpg";
			
		// Check if file already exists
		if(is_file($newfilepath)){
			return $newfilepath;
		}

		// Output resized image
		// Create Image Resource
		$srcimage = $this->imagecreatefrommime($filepath);
		
		// Make image true colour if it's not already
		if (!imageistruecolor($srcimage))
		{
			$tempimage = imagecreatetransparent($width, $height);
			imagecopy($tempimage,$srcimage,0,0,0,0,$width,$height);
			$srcimage = $tempimage;
			unset($tempimage);
		}
		
		// Generate resized image
		$newimage = imagecreatetruecolor($newwidth, $newheight);
		imagefill($newimage, 0, 0, imagecolorallocate($newimage, 255, 255, 255));
		imagecopyresampled($newimage, $srcimage, 0, 0, 0, 0, $newwidth, $newheight, $width, $height);
		imagedestroy($srcimage);
		
		// Add watermark if necessary
		if($attachwm){
			$this->attachwm($newimage);
		}
		
		// Write out image and return
		if(imagejpeg($newimage, $newfilepath, 80)){
			imagedestroy($newimage);
			return $newfilepath;
		}
		else{
			return false;
		}
	}

	// Attach a watermark to an image or image resource.
	private function attachwm(&$image, $wmpos = 'br', $wmimage = false){
		$wmimage = $this->generate_sifnt_wm();
		$wmwidth = imagesx($wmimage);
		$wmheight = imagesy($wmimage);
		
		// TEMP (Should be determined by $wmpos keyword)
		$wmposx = imagesx($image) - ($wmwidth + 2);
		$wmposy = imagesy($image) - ($wmheight + 2);
		// TEMP
		
		imagecopy($image, $wmimage, $wmposx, $wmposy, 0, 0, $wmwidth, $wmheight);
	}

	// Generate sifnt watermark
	private function generate_sifnt_wm(){
	    $text = 'sifnt';
	    $font = public_path() . '/fonts/misterearlbt.ttf';
		$font_size = 48;

	    $textsize = imageftbbox($font_size, 0, $font, $text);
	    $width = $textsize[4] + 10;
	    $height = $textsize[5] - ($textsize[5]*2) + 10;
	    $textimage = $this->imagecreatetransparent($width, $height) or die('Cannot initialize new GD image stream');
	    $text_color = imagecolorallocate($textimage, 0x00, 0x48, 0x9c);
	    $outline_color = imagecolorallocate($textimage, 0xff, 0xff, 0xff);
	    $this->imagettftextoutline($textimage, $font_size, 0, 2, $font_size+2, $text_color, $outline_color, $font, $text, 1);
	    
	    $image = $this->imagecreatetransparent($width, $height/2);
	    imagecopyresampled($image, $textimage, 0, 0, 0, 0, $width, $height/2, $width, $height);
		
		return $image;
	}

	// Draw an outline around text
	private function imagettftextoutline (&$im, $size, $angle, $x, $y, &$col, &$outlinecol, $fontfile, $text, $width){
		// For every X pixel to the left and the right
		for ( $xc =$x -abs ($width ); $xc <= $x +abs ($width ); $xc ++){
			// For every Y pixel to the top and the bottom
	     	for ( $yc =$y -abs ($width ); $yc <= $y +abs ($width ); $yc ++){
				// Draw the text in the outline color
				$text1 = imagettftext ($im ,$size ,$angle ,$xc ,$yc ,$outlinecol ,$fontfile ,$text );
			}
		}
		
		// Draw the main text
		$text2 = imagettftext ($im ,$size ,$angle ,$x ,$y ,$col ,$fontfile ,$text );
	}

	// Rotate an image clockwise and return it's new values as an array
	public function rotateimage($filepath, $angle){
		// Get image details
		$imageinfo = getimagesize($filepath);
		
		// Make angle anti-clockwise
		$angle = 360 - $angle;
		
		// Rotate image
		$image = $this->imagecreatefrommime($filepath);
		$image = imagerotate($image, $angle, 0);
		
		if($this->saveimageasmime($image, $filepath, $imageinfo['mime'])){
			imagedestroy($image);
			
			// Return new image info
			return(getimagesize($filepath));
		}
		else{
			return false;
		}
	}	
}
