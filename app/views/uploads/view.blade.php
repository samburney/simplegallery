@extends('layouts.main')

@section('scripts')
	@parent
	<script type="text/javascript" src="/js/sifntfineuploader.js"></script>
@endsection('scripts')

@section('content')
<div class="row-fluid" style="height: 100%">
	<div class="span10" id="imageView">
		<a href="/get/{{$file_id}}/{{$file_requestedname}}" class="thumbnail"></a>
	</div>
@include('uploads.upload-sidebar')
</div>
<script type="text/javascript">
	$(function(){
		var upload = {{$upload->toJson()}};

		if(upload.extra == 'image'){
			console.log('/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg');
			if(upload.image.width < $('#imageView').width() && upload.image.height < ($(window).height() - 72)){
				$('#imageView').find('a').append($('<img>').attr('src', '/get/' + upload.id + '/{{$file_requestedname}}.' + upload.ext));
			}
			else{
				$('#imageView').find('a').append($('<img>').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg'));
			}
		}
	});
</script>
@endsection('content')