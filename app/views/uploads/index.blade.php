@extends('layouts.main')

@section('scripts')
	@parent
	<script type="text/javascript" src="/js/sifntfineuploader.js"></script>
@endsection('sidebar')

@section('content')
<? $uploads_arr = []; ?>
@foreach ($uploads as $upload)
	<? $uploads_arr[] = $upload->toArray(); ?>
@endforeach		
<div class="row-fluid">
	<div class="span10">
@for ($row=1; $row<=3; $row++)
		<ul class="thumbnails">
	@for ($col=1; $col<=4; $col++)
		<? $i = ($col + ($row - 1) * 4) - 1; ?>
		@if($i < count($uploads_arr))
			<li class="span3">
				<a href="/view/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['originalname']}}.{{$uploads_arr[$i]['ext']}}" class="thumbnail">
					<img src="/get/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['originalname']}}-200x100.jpg">
				</a>
			</li>
		@endif			
	@endfor
		</ul>
@endfor
		<div class="text-center">
			{{$uploads->links()}}
		</div>
	</div>
@include('uploads.upload-sidebar')
</div>
@endsection('content')