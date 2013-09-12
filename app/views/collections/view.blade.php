@extends('layouts.main')

@section('scripts')
	@parent
<script type="text/javascript">
	$(function(){
		$(".thumbnail-row a").smoothScroll({
			afterScroll: function()
			{
				window.location.hash = this.hash;
			}
		});
	});
</script>
@endsection

@section('page_title')
	//
	@if(Request::is('collection/*') || Request::is('collections/*'))
		Collection
	@else
		Tag
	@endif
	- {{$collection->name}}
@endsection

@section('content')
<div class="row">
	<div class="col-lg-9 col-md-10 col-sm-12 text-center">
<? $uploads_arr = []; ?>
@foreach($uploads as $upload)
<? $uploads_arr[] = $upload->toArray(); ?>
		<div id="upload-{{$upload->id}}" class="row text-center thumbnail-row">		
			<a href="{{baseURL()}}/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}" class="img-thumbnail">
				<img src="{{baseURL()}}/get/{{$upload->id}}/{{$upload->cleanname}}-710.jpg">
			</a><br />
			<a href="{{baseURL()}}/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}" style="color: black;">{{$upload->originalname}}</a>
		</div>
@endforeach
@if($uploads->getLastPage() > 1)
		<div class="row text-center">
			{{$uploads->links()}}
		</div>
@endif
	</div>
	<div class="col-lg-3 col-md-2">
		<div class="well well-sm row" style="white-space: nowrap; overflow: hidden;">
			<p class="text-center">
				<b title="{{$collection->name}}">{{$collection->name}}</b>
			</p>
			<p style="font-size: 12px;">
@if(Request::is('collection/*') || Request::is('collections/*'))
				<b>Owner:</b> {{User::find($collection->user_id)->username}}<br>
@endif
				<b>Files:</b> {{count($collection->uploads)}}<br>
			</p>
		</div>
		<div class="row">
			<a href="{{baseURL()}}/{{(Request::is('collection/*') || Request::is('collections/*')) ? 'collection' : 'tag'}}/get/{{(Request::is('collection/*') || Request::is('collections/*')) ? $collection->name_unique : $collection->name}}" class="btn btn-lg btn-success btn-block" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-arrow-down"></span> Download</a>
		</div>
		<div class="well well-sm text-center row hidden-sm hidden-xs">
@for ($row=1; $row<=3; $row++)
@if((count($uploads_arr) + 3) - ($row * 3))
			<div class="row thumbnail-row">
	@for ($col=1; $col<=3; $col++)
		<? $i = ($col + ($row - 1) * 3) - 1; ?>
		@if($i < count($uploads_arr))
				<div class="col-lg-4 col-md-4 text-center">
					<a href="#upload-{{$uploads_arr[$i]['id']}}">
						<img src="{{baseURL()}}/get/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}-100x100.jpg" class="img-thumbnail img-thumbnail-sm">
					</a>
				</div>
		@endif			
	@endfor
			</div>
@endif
@endfor
@if($uploads->getLastPage() > 1)
			<div class="pagination-small row" style="margin-bottom: -20px;">
				{{$uploads->links()}}
			</div>
@endif
		</div>
		<div class="hidden-sm hidden-xs">
			@include('includes.upload-sidebar')
		</div>
	</div>
</div>
@endsection