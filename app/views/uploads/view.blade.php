@extends('layouts.main')

@section('scripts')
	@parent
	<script type="text/javascript" src="/js/sifntfineuploader.js"></script>
@endsection('scripts')

@section('content')
<div class="row-fluid" style="height: 100%">
	<div class="span10 image-small" id="imageView">
		<a href="/get/{{$file_id}}/{{$upload->cleanname}}.{{$upload->ext}}">
			<img src="/get/{{$file_id}}/{{$upload->cleanname}}-780.{{$upload->ext}}?{{uniqid()}}">
		</a>
	</div>
	<div class="span2">
		@include('uploads.upload-sidebar')
		<div class="well well-empty">
			<div class="well-inner">
				<p><b title="{{$upload->originalname}}.{{$upload->ext}}">{{$upload->originalname}}</b></p>
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
@if ($upload->user_id == $user->id)
		<div class="well well-small">
			<ul class="nav nav-stacked nav-pills nav-actions">
	@if ($upload->extra == 'image')
				<li class="dropdown">
					<a href="#" class="dropdown-toggle warning" data-toggle="dropdown">
						Rotate
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="/upload/rotate/{{$upload->id}}/90">90&deg; Clockwise</a></li>
						<li><a href="/upload/rotate/{{$upload->id}}/180">180&deg;</a></li>
						<li><a href="/upload/rotate/{{$upload->id}}/270">90&deg; Counter-Clockwise</a></li>
					</ul>
				</li>
	@endif				
				<li class="danger">
					<a href="#" id="delete-button">Delete</a>
				</li>
			</ul>				
		</div>
@endif
	</div>
</div>
<script type="text/javascript">
	$(function(){
		var upload = {{$upload->toJson()}};

		// Replace image with one that's relevant to the viewport
		if(upload.extra == 'image'){
			if(upload.image.width < $('#imageView').width() && upload.image.height < ($(window).height() - 72)){
				$('#imageView').find('img').addClass('img-polaroid').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '.' + upload.ext + '?{{uniqid()}}');
			}
			else{
				$('#imageView').find('img').addClass('img-polaroid').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg?{{uniqid()}}');
			}
		}
		else{
			$('#imageView').find('img').addClass('img-polaroid').attr('src', '/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + ($(window).height() - 90) + '.jpg?{{uniqid()}}');
		}

		// Handle Delete action
		$('#delete-button').click(function(){
			bootbox.dialog('Are you sure?', [{
				"label": "Yes",
				"class": "btn-danger",
				"callback": function(){
					window.location = '/upload/delete/' + upload.id;
				}
			},
			{
				"label": "No",
				"class": "btn"
			}]);
		});
	});
</script>
@endsection('content')