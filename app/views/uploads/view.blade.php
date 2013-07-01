@extends('layouts.main')

@section('scripts')
	@parent
	<script type="text/javascript" src="/js/sifntfineuploader.js"></script>
@endsection('scripts')

@section('content')
<div class="row-fluid" style="height: 100%">
	<div class="span10" id="imageView">
		<a href="/get/{{$file_id}}/{{$upload->cleanname}}.{{$upload->ext}}" class="thumbnail"></a>
	</div>
	<div class="span2">
		@include('uploads.upload-sidebar')
		<div class="well" style="padding: 0px;">
			<div style="margin: 10px; whitespace: no-wrap; overflow: hidden;">
				<p><b title="{{$upload->originalname}}">{{$upload->originalname}}</b></p>
				<p style="font-size: 12px;">
					<b>Size:</b> {{$upload->size}} Bytes<br>
					<b>Owner:</b> {{User::find($upload->user_id)->pluck('username')}}<br>
					<b>Type:</b> {{$upload->type}}<br>
				</p>
@if ($upload->extra == 'image')
				<p style="font-size: 12px;">
					<b>Metrics:</b> {{$upload->image->width}}x{{$upload->image->height}}<br>
					<b>Bitrate:</b> {{$upload->image->bits}}<br>
				</p>
@endif
			</div>
		</div>
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var upload = {{$upload->toJson()}};

		if(upload.extra == 'image'){
			if(upload.image.width < $('#imageView').width() && upload.image.height < ($(window).height() - 72)){
				$('#imageView').find('a').append($('<img>').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '.' + upload.ext));
			}
			else{
				$('#imageView').find('a').append($('<img>').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg'));
			}
		}
		else{
			$('#imageView').find('a').append($('<img>').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg'));
		}
	});
</script>
@endsection('content')