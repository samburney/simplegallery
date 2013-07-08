<?php
/**
* 
*/
class UploadController extends BaseController
{
	protected $layout = 'layouts.main';
	public $user;

	public function __construct()
	{
		// Autologin if possible
		if(!Auth::check()){
			$userauth = new sifntUserAuth();
			if($user_id = $userauth->getUserId($userauth->getUserName())){
				Auth::loginUsingId($user_id);
			}
			
			//if(Auth::check()){  // FIXME, I don't like this showing up two requests in a row
			//	Session::flash('warning', "You've been automatically logged in as " . Auth::user()->username . '. <a href="#">Why?</a>');
			//}
		}

		$this->user = Auth::user();
	}

	public function getIndex()
	{
		$uploads = $this->user->uploads()->with('image')->orderBy('created_at', 'desc')->paginate(12);

		$this->layout->content = View::make('uploads/index')
			->with('uploads', $uploads);
	}

	public function getPopular()
	{
		$uploads = Upload::popular()->with('image')->orderBy('viewed', 'desc')->paginate(12);

		$this->layout->content = View::make('uploads/index')
			->with('uploads', $uploads);
	}

	public function postIndex()
	{
		$path_preprocess = '/tmp';
		$path_dest = public_path().'/files';
		$allowedExtensions = array();
		$sizeLimit = 200 * 1024 * 1024; // 200MB

		$uploader = new qqFileUploader($allowedExtensions, $sizeLimit);
		$uploaderForm = new qqUploadedFileForm();

		$upload_result = $uploader->handleUpload($path_preprocess);
		$processor = new sifntFileUpload($path_dest);

		if($upload_result['success'] == true){
			$uploadData = $processor->getUploadData($path_preprocess, $uploader->getUploadName(), array_merge($_POST, $_FILES));
		}

		if($upload_result['success'] == true){
			$result['success'] = $processor->processFile($uploadData);
			$result['data'] = $uploadData;
		}
		else{
			$result = $upload_result;
		}

		return Response::json($result);
	}

	public function getView($file_id, $file_requestedname = null)
	{
		$upload = Upload::with('image', 'tags')->find($file_id);
		
		$tags = implode(',', $upload->tags->lists('name'));

		$this->layout->content = View::make('uploads/view')
			->with('upload', $upload)
			->with('tags', $tags)
			->with('file_id', $file_id)
			->with('file_requestedname', $file_requestedname);
	}

	public function getGet($file_id, $file_requestedname)
	{
	    // Check referrer. TODO
	    // Check for blank referrer
	    /*if(!$_SERVER['HTTP_REFERER'] && $context['allow_blank_referrer']){
	        $referrer_approved = 1;
	    }
	    else{
	        $sql = "select * from ".$db_tp."referrers";
	        $result = $db->queryAll($sql);
	        if(checkdberror($result)){
	            foreach($result as $referrer){
	                if(false !== strpos($_SERVER['HTTP_REFERER'], $referrer['referrer_hostname'])){
	                    if($referrer['referrer_banned']){
	                        $referrer_banned = 1;
	                    }
	                    else{
	                        $referrer_approved = 1;
	                    }
	                }
	            }
	        }
	    }*/
	 
	    // Determine if options have been specified
	    /*$file_id = $path[0];
	    if(sizeof($path) == 2){
	        $file_requestedname = $path[1];
	    }
	    else{
	        for($i=1; $i<(sizeof($path)-1); $i++){
	            $split = explode(':', $path[$i]);
	            
	            $options[$split[0]] = $split[1];
	        }
	        $file_requestedname = $path[sizeof($path)-1];
	    }*/
	    
	    // Split requested name
	    $namesplit = sifntFileUpload::namesplit(public_path() . "/$file_requestedname"); // TODO: Need to fix this function to not *require* a full path
	    $file_requestedname = $namesplit[2];
	    $file_requestedext = $namesplit[3];

	    // Get file data
	    $upload = Upload::with('image')->find($file_id);
	    if($upload){
	    	// Create $file array
	    	$file = array(
	    		'id' => $upload->id,
	    		'description' => $upload->description,
	    		'name' => $upload->name,
	    		'cleanname' => $upload->cleanname,
	    		'originalname' => $upload->originalname,
	    		'ext' => $upload->ext,
	    		'size' => $upload->size,
	    		'type' => $upload->type,
	    		'extra' => $upload->extra,
	    	);

	    	// Ensure original file exists
	    	$filepath = public_path() . '/files/' . $file['name'] . '.' . $file['ext'];
	    	if(!is_file($filepath)){
	    		header('HTTP/1.0 404 Not Found');
	    		die('File not found');
	    	}

	    	$convert = new sifntFileConvert();

	    	// If a file conversion request exists, perform conversion (Where possible)
	    	if($file_requestedext != $upload->ext){
	    	    $convert->convertfile($file, $file_requestedext);
		    	$filepath = public_path() . '/files/' . $file['name'] . '.' . $file['ext'];
	    	}

	    	// Process filetype specific extras
	    	switch($file['extra']){
	    	    case 'image':
	    	        // Dynamic image resize
	    	        if(preg_match('/^([a-z0-9\_.\-% ]+)\-((thumb|large|small)|([0-9]+|[0-9]+x[0-9]+))$/i', $file_requestedname, $file_requestedname_arr)){
							if($newfilepath = $convert->resizeimage($filepath, $file_requestedname_arr[2])){
	    	                $filepath = $newfilepath;
	    	                
	    	                $imageinfo = getimagesize($filepath);
	    	                $file['type'] = $imageinfo['mime'];
	    	                $file['size'] = filesize($filepath);
	    	            }
	    	        }
	    	        
	    	        /*// Deal with referrers TODO
	    	        // If not an approved referrer, hand them a -small image
	    	        if(!$referrer_approved){
	    	            if($newfilepath = resizeimage($filepath, "small")){
	    	                $filepath = $newfilepath;
	    	                
	    	                $imageinfo = getimagesize($filepath);
	    	                $file_type = $imageinfo['mime'];
	    	                $file_size = filesize($filepath);
	    	            }                
	    	        }
	    	        
	    	        // If a banned referrer, hand them nothing
	    	        if($referrer_banned){
	    	            header('HTTP/1.0 404 Not Found');
	    	            die('This site has been banned for image leeching.');
	    	        }*/
	    	        
	    	        break;
	    	}

    	    // Push file to user    
    	    if(is_file($filepath)){
    	    	Upload::find($file['id'])->increment('viewed');

	            $handle = fopen($filepath, 'rb');
	            
	            header("Content-type: " . $file['type']);
	            header("Content-Length: " . $file['size']);
	            header("Content-disposition: inline; filename=\"$file_requestedname." . $file['ext']);
	            header("Cache-Control: max-age=31536000");
	            header("Expires:");
				header("Pragma:");
	            
	            $content = fpassthru($handle);
	        }
	        else{
	        	header('HTTP/1.0 404 Not Found');
	        	die('File not found');	        	
	        }
	        
	        exit();
	    }
	    else{
            header('HTTP/1.0 404 Not Found');
            die('File not found');
	    }
	}

	public function getRotate($id, $angle)
	{
		$sifntUpload = new sifntFileUpload();
		$convert = new sifntFileConvert();
		$upload = Upload::with('image')->find($id);
		$file_path = public_path() . "/files/$upload->name.$upload->ext";

		if(Auth::user()->id != $upload->user_id){
			return Redirect::to(URL::previous())
				->with('error', "You don't have permission to do that");
		}

		if($imageinfo = $convert->rotateimage($file_path, $angle)){
			$upload->image->width = $imageinfo[0];
			$upload->image->height = $imageinfo[1];

			$upload->image->save();

			$sifntUpload->deletefilecache($id);

			return Redirect::to(URL::previous())
				->with('notice', 'Image successfully rotated ' . $angle . '&deg;');
		}
		else{
			return Redirect::to(URL::previous())
				->with('error', 'Image rotation failed');
		}
	}

	public function getDelete($id)
	{
		$upload = Upload::find($id);
		$sifntUpload = new sifntFileUpload();

		if(Auth::user()->id != $upload->user_id){
			return Redirect::to(URL::previous())
				->with('error', "You don't have permission to do that");
		}

		if($sifntUpload->deletefile($id)){
			return Redirect::route('home')
				->with('notice', "Deleted $upload->originalname.$upload->ext");
		}
		else{
			return Redirect::to(URL::previous())
				->with('error', 'File deletion failed');
		}
	}

	public function postSetprivate()
	{
		$upload_id = Input::get('upload_id');
		$private = Input::get('private') == 'true' ? 1 : 0;

		$upload = Upload::find($upload_id);

		if(Auth::user()->id != $upload->user_id){
			return Redirect::to(URL::previous())
				->with('error', "You don't have permission to do that");
		}

		$upload->private = $private;
		$upload->save();

		return Response::json();
	}
}
?>