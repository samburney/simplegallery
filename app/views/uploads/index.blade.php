@extends('layouts.main')

@section('content')
<? $uploads_arr = []; ?>
@foreach ($uploads as $upload)
	<? $uploads_arr[] = $upload->toArray(); ?>
@endforeach		
<div class="row-fluid">
	<div class="span10">
		<ul class="nav nav-pills">
			<li class="active">
				<a href="/">Recent Uploads</a>
			</li>
			<li>
				<a href="/collections">Recent Collections</a>
			</li>
			<li>
				<a href="/tags">Tags</a>
			</li>
		</ul>
@for ($row=1; $row<=3; $row++)
		<ul class="thumbnails">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($uploads_arr))
			<li class="span3">
				<div class="image-thumbnail">
					<a href="/view/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}.{{$uploads_arr[$i]['ext']}}">
						<img src="/get/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}-200x100.jpg" class="img-polaroid">
					</a>
				</div>
				<div style="text-align: center; white-space: nowrap; overflow: hidden;">
					<a href="/view/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}.{{$uploads_arr[$i]['ext']}}" style="color: black;">
						<small>
							{{$uploads_arr[$i]['originalname']}}
						</small>
					</a>
				</div>
			</li>
		@endif			
	@endfor
		</ul>
@endfor
		<div class="text-center">
			{{$uploads->links()}}
		</div>
	</div>
	<div class="span2">
		@include('uploads.upload-sidebar')
	</div>
</div>
@endsection('content')