<?php
class SearchController extends BaseController
{
	public function searchIndex($q = null)
	{
		$q = isset($q) ? $q : Input::get('q');

		$uploads = Upload::where('originalname', 'LIKE', "%$q%")
			->where(function($query) {
				$query->where('user_id', '=', $this->user->id)
					->orWhere('private', '=', 0);
			})
			->paginate(12);
		$collections = $this->user->collections()->where('name', 'LIKE', "%$q%")->paginate(12);
		$tags = Tag::where('name', 'LIKE', "%$q%")->paginate(12);

		return View::make('search/index')
			->with(compact('uploads', 'tags', 'collections', 'q'));
	}

	public function searchGet($q) {
		$uploads = Upload::where('originalname', 'LIKE', "%$q%")
			->where(function($query) {
				$query->where('user_id', '=', $this->user->id)
					->orWhere('private', '=', 0);
			})
			->get();

		sifntFileUtil::createZip($q, $uploads->toArray());
	}
}