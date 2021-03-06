@extends('layouts.main')

@section('page_title')
	// {{$upload->originalname}}
@endsection

@section('scripts')
	@parent
	<script type="text/javascript">
		$(function(){
			var upload = {{$upload->toJson()}};
			var file_requestedext = '{{$file_requestedext}}';

			// Replace image with one that's relevant to the viewport
			if(upload.extra == 'image'){
				if(upload.image.width > ($(window).width() - 10) || upload.image.height > ($(window).height() - 10)) {
					$('#imageView > a').attr('href', '{{baseURL()}}/get/{{$file_id}}/{{$upload->cleanname}}-' + ($(window).width() - 10) + 'x' + ($(window).height() - 10) + '.jpg');
				}

				if(upload.image.width < $('#imageView').width()){
					$('#imageView').find('img').addClass('img-thumbnail img-thumbnail-bordercollapse').attr('src', '{{baseURL()}}/get/' + upload.id + '/' + upload.cleanname + '.' + file_requestedext + '?{{Session::get('uniqid')}}');
				}
				else{
					$('#imageView').find('img').addClass('img-thumbnail img-thumbnail-bordercollapse').attr('src', '{{baseURL()}}/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + 999999 + '.jpg?{{Session::get('uniqid')}}');
				}
			}
			else{
				$('#imageView').find('img').addClass('img-thumbnail img-thumbnail-bordercollapse').attr('src', '{{baseURL()}}/get/' + upload.id + '/' + upload.cleanname + '-' + $('#imageView').width() + 'x' + 999999 + '.jpg?{{Session::get('uniqid')}}');
			}

			$('#imageView').find('img').show().removeClass('hide');

			// Handle Delete action
			$('#delete-button').click(function(){
				bootbox.dialog({
					message: 'Are you sure?',
					title: "Confirm Delete",
					label: "Yes",
					buttons: {
						Yes: {
							className: "btn-danger",
							callback: function(){
								window.location = '{{baseURL()}}/upload/delete/' + upload.id;
							}
						},
						Cancel: {
								className: "btn"
							}
						}
					});
			});	

			// Set up tags select
			$('#tags')
				.select2({
					tags: true,
					ajax: {
						url: '{{baseURL()}}/tag/query',
						dataType: 'json',
						data: function(term, page){
							return {
								q: term
							};
						},
						results: function (data, page){
							return {
								results: data.results
							};
						}
					},
					initSelection: function(element, callback){
						var data = [];
						$(element.val().split(",")).each(function(index, value){
							data.push({id: this, text: this});
						});
						callback(data);
					},
					placeholder: "Tag this now!",
					minimumInputLength: 2,
					tokenSeparators: [","],
					triggerChange: true,
				})
				.change(function(e){
					if(e.removed){
						$.post(
							'{{baseURL()}}/tag/removetag',
							{
								file_id: upload.id,
								tag: e.removed.text,
							},
							function(data){},
							'json'
						);					
					}
					else{
						$.post(
							'{{baseURL()}}/tag/process',
							{
								file_id: upload.id,
								tags: $('#tags').val(),
							},
							function(data){},
							'json'
						);					
					}
				});

				$('#privateCB').change(function(){
					$.post(
						baseURL + '/upload/setprivate',
						{
							upload_id: upload.id,
							private: $(this).prop('checked'),
						}
					)
				});


				$('#addUploadToCollection').click(function(){
					upload_ids = [upload.id];

					$('#collectionModal').modal();
				});

				$('.fancybox').fancybox({
					padding: 0,
					margin: 5,
					closeBtn: false,
					closeClick: true,
					helpers: {
						title: {
							type: 'over'
						}
					},
					afterLoad: function() {
						this.title = '<b><a href="{{baseURL()}}/get/{{$file_id}}/{{$upload->cleanname}}.{{$file_requestedext}}">' + this.title + '</a></b>'
					}
				});
			});
	</script>
@endsection

@section('content')
<div class="row">
	<div class="col-lg-10 col-md-10 col-sm-12">
		<div class="row text-center" id="imageView">
			<a href="{{baseURL()}}/get/{{$file_id}}/{{$upload->cleanname}}.{{$file_requestedext}}" title="{{$upload->description}}" class="fancybox" title="{{$upload->description}}">
				<img style="display: none;">
			</a>
		</div>
	</div>
	<div class="col-lg-2 col-md-2">
		<div class="hidden-sm hidden-xs">
			@include('includes.upload-sidebar')
		</div>
		<div class="row text-center">
			@if($upload->extra == 'image')
			<div class="btn-group">
				<a href="{{baseURL()}}/upload/get/{{$upload->id}}/{{$upload->originalname}}.{{$upload->ext}}" class="btn btn-default btn-success btn-lg" style="margin-bottom: 20px;">
					Download
				</a>
				<button type="button" class="btn btn-success dropdown-toggle btn-lg" data-toggle="dropdown">
					<span class="caret"></span>
				</button>
				<ul class="dropdown-menu" role="menu">
					@if(strtolower($upload->ext) != 'jpg' && strtolower($upload->ext) != 'jpeg' && strtolower($upload->ext) != 'jpe')
					<li><a href="{{baseURL()}}/upload/get/{{$upload->id}}/{{$upload->originalname}}.jpg">JPEG</a></li>
					@endif
					@if(strtolower($upload->ext) != 'png')
					<li><a href="{{baseURL()}}/upload/get/{{$upload->id}}/{{$upload->originalname}}.png">PNG</a></li>
					@endif
					@if(strtolower($upload->ext) != 'gif')
					<li><a href="{{baseURL()}}/upload/get/{{$upload->id}}/{{$upload->originalname}}.gif">GIF</a></li>
					@endif
				</ul>
			</div>
			@else
			<a href="{{baseURL()}}/upload/get/{{$upload->id}}/{{$upload->originalname}}.{{$upload->ext}}" class="btn btn-default btn-success btn-lg btn-block" style="margin-bottom: 20px;">
				<span class="glyphicon glyphicon-arrow-down"></span>
				Download
			</a>
			@endif
		</div>
		<div class="well well-empty row">
			<div class="well-inner">
				<p><b title="{{$upload->originalname}}.{{$upload->ext}}">{{$upload->originalname}}</b></p>
				<p style="font-size: 12px;">
					<b>Size:</b> {{$upload->size}} Bytes<br>
					<b>Owner:</b> {{User::find($upload->user_id)->username}}<br>
					<b>Type:</b> {{$upload->type}}<br>
				</p>
@if ($upload->extra == 'image')
				<p style="font-size: 12px;">
					<b>Metrics:</b> {{$upload->image->width}}x{{$upload->image->height}}<br>
					<b>Bitrate:</b> {{$upload->image->bits}}<br>
				</p>
@endif
				<p style="font-size: 12px;">
					<b>Tags:</b><br>
					<input type="hidden" id="tags" style="width: 100%;" value="{{$tags}}"></input>
				</p>
@if ($upload->user_id == Auth::user()->id && Auth::user()->id > 0)
				<p style="font-size: 12px;">
					<label class="checkbox" title="The Private flag keeps files out of public lists">
						<small>
							<input type="checkbox" id="privateCB"<? if($upload->private){ ?> checked="checked"<? } ?>>
							<b>Private</b>
						</small>
					</label>
				</p>
@endif				
			</div>
		</div>
@if ($upload->user_id == Auth::user()->id || Auth::user()->id == 1 || Auth::user()->id == 37 )
		<div class="well well-sm row">
			<ul class="nav nav-stacked nav-pills nav-actions">
	@if ($upload->extra == 'image')
				<li class="dropdown">
					<a href="#" class="dropdown-toggle warning" data-toggle="dropdown">
						Rotate
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
						<li><a href="{{baseURL()}}/upload/rotate/{{$upload->id}}/90">90&deg; Clockwise</a></li>
						<li><a href="{{baseURL()}}/upload/rotate/{{$upload->id}}/180">180&deg;</a></li>
						<li><a href="{{baseURL()}}/upload/rotate/{{$upload->id}}/270">90&deg; Counter-Clockwise</a></li>
					</ul>
				</li>
	@endif
				<li class="dropdown">
					<a href="#" class="dropdown-toggle warning" data-toggle="dropdown">
						Collections
						<span class="caret"></span>
					</a>
					<ul class="dropdown-menu">
@if (count($upload->collections))
	@foreach($upload->collections as $collection)
						<li>
							<a href="{{baseURL()}}/collection/view/{{$collection->name_unique}}">
								{{$collection->name}}
							</a>
						</li>
	@endforeach
						<li class="divider"></li>
@endif
						<li><a href="#" id="addUploadToCollection">Add to Collection</a></li>
					</ul>
				</li>
				<li class="danger">
					<a href="#" id="delete-button">Delete</a>
				</li>
			</ul>				
		</div>
@endif
	</div>
</div>
@endsection('content')
