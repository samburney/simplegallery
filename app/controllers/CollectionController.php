<?php
/**
* 
*/
class CollectionController extends BaseController
{
	protected $layout = 'layouts.main';

	public function getIndex()
	{
		$collections = $this->user->collections()->with('uploads', 'uploads.image')->orderBy('name_unique', 'asc')->paginate(12);

		$this->layout->content = View::make('collections/index')
			->with('collections', $collections);
	}

	public function postNew(){
		$result['success'] = false;


		$rules = array(
			'name' => 'required|min:2',
		);
		$validator = Validator::make(Input::all(), $rules);


		if($validator->fails()) {
			$result['errors'] = $validator->messages()->all();
			return Response::json($result);
		}

		$collection = new Collection();
		$collection->name = Input::get('name');
		$collection->name_unique = sifntFileUtil::cleantext($collection->name, "collections");
		$collection->user_id = $this->user->id;

		if($collection->save()){
			$result = [
				'success' => true,
				'collection_id' => $collection->id,
				'name_unique' => $collection->name_unique,
			];
		}

		foreach(Input::get('ids') as $upload_id){
			$collection->uploads()->attach($upload_id);
			TagController::processTags($upload_id, [str_singular(sifntFileUtil::cleantext($collection->name))], true);
		}

		return Response::json($result);
	}

	public function postAddtoexisting(){
		$result['success'] = false;

		$collection_id = Input::get('collection_id');
		$collection = Collection::find($collection_id);

		$result = [
			'success' => true,
			'collection_id' => $collection->id,
			'name_unique' => $collection->name_unique,
		];

		foreach(Input::get('ids') as $upload_id){
			if(!$collection->uploads()->where('id', '=', $upload_id)->first()){
				$collection->uploads()->attach($upload_id);
				TagController::processTags($upload_id, [str_singular(sifntFileUtil::cleantext($collection->name))]);
			}
		}

		return Response::json($result);
	}

	public function getView($id){
		if($collection = Collection::where('name_unique', '=', $id)->first()){
			$id = $collection->id;
		}
		$collection = Collection::with('uploads', 'uploads.image')->find($id);
		//sU::debug($collection->toArray());

		return View::make('collections/view')
			->with('collection', $collection)
			->with('uploads', $collection->uploads()->orderBy('created_at', 'asc')->paginate(9));
	}
}