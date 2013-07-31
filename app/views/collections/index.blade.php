@extends('layouts.main')

@section('page_title')
	// Collections
@endsection

@section('content')
<? $uploads_arr = []; ?>
@foreach ($collections as $collection)
	<? $uploads_arr[] = array('collection' => $collection, 'upload' => $collection->uploads()->first()->toArray()); ?>
@endforeach		
<div class="row">
	<div class="col-lg-10">
@include('includes/top-nav')
@for ($row=1; $row<=3; $row++)
		<div class="row thumbnails-row">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($uploads_arr))
			<div class="col-lg-3 text-center">
				<div class="row">
					<a href="{{baseURL()}}/collection/view/{{$uploads_arr[$i]['collection']['name_unique']}}">
						<img src="{{baseURL()}}/get/{{$uploads_arr[$i]['upload']['id']}}/{{$uploads_arr[$i]['upload']['cleanname']}}-200x100.jpg" class="img-thumbnail">
					</a>
				</div>
				<div style="text-align: center; white-space: nowrap; overflow: hidden;">
					<a href="{{baseURL()}}/collection/view/{{$uploads_arr[$i]['collection']['name_unique']}}" style="color: black;">
						<small>
							{{$uploads_arr[$i]['collection']['name']}}
						</small>
					</a>
				</div>
			</div>
		@endif			
	@endfor
		</div>
@endfor
		<div class="text-center">
			{{$collections->links()}}
		</div>
	</div>
	<div class="col-lg-2">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')