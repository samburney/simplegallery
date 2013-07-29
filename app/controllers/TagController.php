<?php
/**
* 
*/
class TagController extends BaseController
{
	protected $layout = 'layouts.main';

	public function getIndex()
	{
		// This code *should* look like this, but a bug in Laravel4's has() stops it properly supporting conditions
		//$tags = Tag::has('uploads')->with('uploads', 'uploads.image')->orderBy('name', 'asc')->paginate(12);

		// So it looks like this instead...
		$tags_unpaged = [];
		$tags_raw = Tag::with('public_uploads', 'uploads', 'uploads.image')->orderBy('name', 'asc')->get();
		foreach($tags_raw as $tag){
			if(count($tag->public_uploads)) {
				$tags_unpaged[] = $tag->toArray();
			}
		};

		$num_tags = count($tags_unpaged);
		//sU::debug($tags_unpaged);
		$tags_per_page = 12;
		$tags_paged = Paginator::make($tags_unpaged, $num_tags, $tags_per_page);
		
		// Moar hax, apparently the Pagintor doesn't like me
		$tags = array_slice($tags_unpaged, (Paginator::getCurrentPage() - 1) * $tags_per_page, $tags_per_page);
		//sU::debug($tags);
		// END hax



		$this->layout->content = View::make('tags/index')
			->with('tags_paged', $tags_paged)
			->with('tags', $tags);
	}

	public function getView($tag_name)
	{
		$tag = Tag::where('name', '=', $tag_name)->first();

		return View::make('collections/view')
			->with('collection', $tag)
			->with('uploads', $tag->uploads()->paginate(9));
	}

	public function postProcess()
	{
		$file_id = Input::get('file_id');
		$tags = explode(',', Input::get('tags'));

		$success = TagController::processTags($file_id, $tags);

		return Response::json(array('success' => $success));
	}

	public static function processTags($file_id, $tags)
	{
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

		return $success;
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