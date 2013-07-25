@extends('layouts.main')

@section('content')
<? $uploads_arr = []; ?>
@foreach ($collections as $collection)
	<? $uploads_arr[] = array('collection' => $collection, 'upload' => $collection['uploads'][0]); ?>
@endforeach		
<div class="row-fluid">
	<div class="span10">
@include('includes/top-nav')
@for ($row=1; $row<=3; $row++)
		<ul class="thumbnails">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($uploads_arr))
			<li class="span3">
				<div class="image-thumbnail">
					<a href="{{baseURL()}}/tag/view/{{$uploads_arr[$i]['collection']['name']}}">
						<img src="{{baseURL()}}/get/{{$uploads_arr[$i]['upload']['id']}}/{{$uploads_arr[$i]['upload']['cleanname']}}-200x100.jpg" class="img-polaroid">
					</a>
				</div>
				<div style="text-align: center; white-space: nowrap; overflow: hidden;">
					<a href="{{baseURL()}}/tag/view/{{$uploads_arr[$i]['collection']['name']}}" style="color: black;">
						<small>
							{{$uploads_arr[$i]['collection']['name']}}
						</small>
					</a>
				</div>
			</li>
		@endif			
	@endfor
		</ul>
@endfor
		<div class="text-center">
			{{$collections->links()}}
		</div>
	</div>
	<div class="span2">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')