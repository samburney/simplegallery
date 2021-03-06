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
	<div class="col col-lg-10 col-md-10 col-sm-12">
@for ($row=1; $row<=3; $row++)
		<div class="row thumbnail-row">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($uploads_arr))
			<div class="col-lg-3 col-md-3 col-sm-3 text-center">
				<div>
					<a title="{{$uploads_arr[$i]['originalname']}}" href="{{URL::to('view/' . $uploads_arr[$i]['id'] . '/' . $uploads_arr[$i]['cleanname'] . '.' . $uploads_arr[$i]['ext'])}}">
						<img class="img-thumbnail" src="{{URL::to('get/' . $uploads_arr[$i]['id'] . '/' . $uploads_arr[$i]['cleanname'] . '-768x576.jpg')}}" class="img-polaroid">
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
		@if (method_exists($uploads, 'links'))
			{{$uploads->links()}}
		@endif
		</div>
	</div>
	<div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')