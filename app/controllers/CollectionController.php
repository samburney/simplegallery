<?php
/**
* 
*/
class CollectionController extends BaseController
{
	public $user;

	public function __construct()
	{
		$this->user = Auth::user();
	}

	public function postNew(){
		$result['success'] = false;

		$collection = new Collection();
		$collection->name = Input::get('name');
		$collection->user_id = $this->user->id;

		if($collection->save()){
			$result = [
				'success' => true,
				'collection_id' => $collection->id,
			];
		}

		foreach(Input::get('ids') as $upload_id){
			$collection->uploads()->attach($upload_id);
		}

		return Response::json($result);
	}

	public function getView($id){
		$collection = Collection::with('uploads', 'uploads.image')->find($id);
		//sU::debug($collection->toArray());

		return View::make('collections/view')
			->with('collection', $collection)
			->with('uploads', $collection->uploads()->paginate(9));
	}
}