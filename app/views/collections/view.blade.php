@extends('layouts.main')

@section('scripts')
	@parent
<script type="text/javascript">
	$(function(){
		$(".image-thumbnail a").click(function(e){
			$('html,body').scrollTo(this.hash, this.hash);
			window.location.hash = this.hash;
			e.preventDefault();
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
	<div class="col-lg-9">
<? $uploads_arr = []; ?>
@foreach($uploads as $upload)
<? $uploads_arr[] = $upload->toArray(); ?>
		<div id="upload-{{$upload->id}}" class="row" style="margin-bottom: 10px;">		
			<a href="{{baseURL()}}/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}">
				<img src="{{baseURL()}}/get/{{$upload->id}}/{{$upload->cleanname}}-710.{{$upload->ext}}" class="img-thumbnail">
			</a>
		</div>
@endforeach
	</div>
	<div class="col-lg-3">
		<div class="well well-small row">
			<p class="text-center">
				<b>{{$collection->name}}</b>
			</p>
			<p style="font-size: 12px;">
@if(Request::is('collection/*') || Request::is('collections/*'))
				<b>Owner:</b> {{User::find($collection->user_id)->username}}<br>
@endif
				<b>Files:</b> {{count($collection->uploads)}}<br>
			</p>
		</div>
		<div class="well well-small text-center row">
@for ($row=1; $row<=3; $row++)
@if((count($uploads_arr) + 3) - ($row * 3))
			<div class="thumbnails-tiny-row row">
	@for ($col=1; $col<=3; $col++)
		<? $i = ($col + ($row - 1) * 3) - 1; ?>
		@if($i < count($uploads_arr))
				<div class="col-lg-4 text-center">
					<a href="#upload-{{$uploads_arr[$i]['id']}}">
						<img src="{{baseURL()}}/get/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}-60x60.jpg" class="img-thumbnail img-polaroid-tiny">
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
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection