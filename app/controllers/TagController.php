<?php
/**
* 
*/
class TagController extends BaseController
{
	public function postProcess()
	{
		$file_id = Input::get('file_id');
		$tags = explode(',', Input::get('tags'));

		Upload::find($file_id)->tags()->detach();
		foreach($tags as $tag_name){
			$success = true;

			$tag_name = sifntFileUtil::cleantext($tag_name);

			if(!$tag = Tag::where('name', '=', $tag_name)->first()){
				$tag = new Tag();
				$tag->name = $tag_name;
				if(!$tag->save()){
					$success = false;
				}
			}

			$tag->uploads()->attach($file_id);
		}

		return Response::json(array('success' => $success));
	}

	public function postRemovetag()
	{
		$file_id = Input::get('file_id');
		$tag = Input::get('tag');
		$success = false;

		$tag_id = Tag::where('name', '=', $tag)->first()->id;
		if(Upload::find($file_id)->tags()->detach($tag_id)){
			$success = true;

			// Check if this is the last use of this tag
			if(!Tag::has('uploads')->find($tag_id)) {
				Tag::find($tag_id)->delete();
			}

		}

		return Response::json(array('success' => $success));
	}

	public function getQuery()
	{
		$q = sifntFileUtil::cleantext(Input::get('q'));
		$success = false;

		if($tags = Tag::where('name', 'LIKE', "$q%")->lists('name')){
			$success = true;
		}

		$results = [];

		if(!in_array($q, $tags)){
			$results[] = [
				'id' => $q,
				'text' => $q,
			];
		}

		foreach($tags as $tag){
			$results[] = [
				'id' => $tag,
				'text' => $tag,
			];
		}

		$data = [
			'success' => $success,
			'more' => false,
			'results' => $results,
		];
		return Response::json($data);
	}
}