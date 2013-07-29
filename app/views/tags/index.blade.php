@extends('layouts.main')

@section('page_title')
	// Tags
@endsection

@section('content')
<div class="row-fluid">
	<div class="span10">
@include('includes/top-nav')
@for ($row=1; $row<=3; $row++)
		<ul class="thumbnails">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($tags))
			<li class="span3">
				<div class="image-thumbnail">
					<a href="{{baseURL()}}/tag/view/{{$tags[$i]['name']}}">
						<img src="{{baseURL()}}/get/{{$tags[$i]['uploads'][0]['id']}}/{{$tags[$i]['uploads'][0]['cleanname']}}-200x100.jpg" class="img-polaroid">
					</a>
				</div>
				<div style="text-align: center; white-space: nowrap; overflow: hidden;">
					<a href="{{baseURL()}}/tag/view/{{$tags[$i]['name']}}" style="color: black;">
						<small>
							{{$tags[$i]['name']}}
						</small>
					</a>
				</div>
			</li>
		@endif			
	@endfor
		</ul>
@endfor
		<div class="text-center">
			{{$tags_paged->links()}}
		</div>
	</div>
	<div class="span2">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')