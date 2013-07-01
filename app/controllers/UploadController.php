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
		// Autologin for now
		if(!Auth::check()){
			$userauth = new sifntUserAuth();
			$user_id = $userauth->getUserId($userauth->getUserName());

			Auth::loginUsingId($user_id);
		}

		$this->user = Auth::user();

		View::composer('layouts.main', function($view)
			{
				$view->with('user', $this->user);
			}
		);
	}

	public function getIndex()
	{
		$uploads = $this->user->uploads()->with('image')->orderBy('created_at', 'desc')->paginate(12);

		$this->layout->content = View::make('uploads/index')
			->with('uploads', $uploads);
	}

	public function getProto()
	{
		$this->layout->content = View::make('uploads/proto');
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

	public function getView($file_id, $file_requestedname)
	{
		$upload = Upload::with('image')->find($file_id);

		$this->layout->content = View::make('uploads/view')
			->with('upload', $upload)
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
    	    	// Increment 'viewed'
    	    	DB::table('uploads')->where('id', $file['id'])->increment('viewed');

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
}
?>