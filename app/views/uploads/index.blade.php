@extends('layouts.main')

@section('page_title')
	@if(isset($page_title))
		// {{$page_title}}
	@endif
@endsection

@section('content')
<? $uploads_arr = []; ?>
@foreach ($uploads as $upload)
	<? $uploads_arr[] = $upload->toArray(); ?>
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
				<div>
					<a title="{{$uploads_arr[$i]['originalname']}}" href="{{URL::to('view/' . $uploads_arr[$i]['id'] . '/' . $uploads_arr[$i]['cleanname'] . '.' . $uploads_arr[$i]['ext'])}}">
						<img class="img-thumbnail" src="{{URL::to('get/' . $uploads_arr[$i]['id'] . '/' . $uploads_arr[$i]['cleanname'] . '-200x100.jpg')}}" class="img-polaroid">
					</a>
				</div>
				<div style="text-align: center; white-space: nowrap; overflow: hidden;">
					<a title="{{$uploads_arr[$i]['originalname']}}" href="{{URL::to('view/' . $uploads_arr[$i]['id'] . '/' . $uploads_arr[$i]['cleanname'] . '.' . $uploads_arr[$i]['ext'])}}" style="color: black;">
						<small>
							{{$uploads_arr[$i]['originalname']}}
						</small>
					</a>
				</div>
			</div>
		@endif			
	@endfor
		</div>
@endfor
		<div class="text-center">
			{{$uploads->links()}}
		</div>
	</div>
	<div class="col-lg-2">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')