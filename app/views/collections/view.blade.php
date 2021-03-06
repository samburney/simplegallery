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
				console.log($(this).attr('class'));
				this.title =  '<b><a href="' + $(this.element).attr('href') + '">' + this.title + '</a></b> | <a href="' + $(this.element).data('original-file') + '">Download</a>'
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
@if($uploads->getCurrentPage() > 1)
		<div class="hide">
	@for($i = 0; $i < ($uploads->getFrom() - 1); $i++)
			<a
		@if($unpaged_uploads[$i]['extra'] == 'image' && ($unpaged_uploads[$i]['image']['height'] > (Session::get('height') - 10) || $unpaged_uploads[$i]['image']['width'] > (Session::get('width') - 10)))
				data-fancybox-href="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}-{{Session::get('width') - 10}}x{{Session::get('height') - 10}}.jpg"
		@else
				data-fancybox-href="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}.{{$unpaged_uploads[$i]['ext']}}"
		@endif
				class="fancybox"
				rel="gallery-{{(Request::is('collection/*') || Request::is('collections/*')) ? $collection['name_unique'] : $collection['name']}}"
				title="{{$unpaged_uploads[$i]['description']}}"
				href="{{baseURL()}}/view/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}.{{$unpaged_uploads[$i]['ext']}}"
				data-original-file="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['originalname']}}.{{$unpaged_uploads[$i]['ext']}}"
			></a>
	@endfor
		</div>
@endif
<? $uploads_arr = []; ?>
@foreach($uploads as $upload)
<? $uploads_arr[] = $upload->toArray(); ?>
		<div id="upload-{{$upload->id}}" class="row text-center thumbnail-row image-view">
			<a
@if($upload->extra == 'image' && ($upload->image->height > (Session::get('height') - 10) || $upload->image->width > (Session::get('width') - 10)))
				data-fancybox-href="{{baseURL()}}/get/{{$upload->id}}/{{$upload->cleanname}}-{{Session::get('width') - 10}}x{{Session::get('height') - 10}}.jpg"
@else
				data-fancybox-href="{{baseURL()}}/get/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}"
@endif
				class="fancybox"
				rel="gallery-{{(Request::is('collection/*') || Request::is('collections/*')) ? $collection->name_unique : $collection->name}}"
				title="{{$upload->description}}"
				href="{{baseURL()}}/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}"
				data-original-file="{{baseURL()}}/get/{{$upload->id}}/{{$upload->originalname}}.{{$upload->ext}}"
				>
				<img src="{{baseURL()}}/get/{{$upload->id}}/{{$upload->cleanname}}-{{Session::get('width') > 863 ? 863 : Session::get('width')}}.jpg" class="img-thumbnail">
			</a><br />
			<a href="{{baseURL()}}/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}" style="color: black;">{{$upload->originalname}}</a>
		</div>
@endforeach
@if($uploads->getCurrentPage() < $uploads->getLastPage())
		<div class="hide">
	@for($i = $uploads->getTo(); $i < count($unpaged_uploads); $i++)
			<a
		@if($unpaged_uploads[$i]['extra'] == 'image' && ($unpaged_uploads[$i]['image']['height'] > (Session::get('height') - 10) || $unpaged_uploads[$i]['image']['width'] > (Session::get('width') - 10)))
				data-fancybox-href="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}-{{Session::get('width') - 10}}x{{Session::get('height') - 10}}.jpg"
		@else
				data-fancybox-href="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}.{{$unpaged_uploads[$i]['ext']}}"
		@endif
				class="fancybox"
				rel="gallery-{{(Request::is('collection/*') || Request::is('collections/*')) ? $collection['name_unique'] : $collection['name']}}"
				title="{{$unpaged_uploads[$i]['description']}}"
				href="{{baseURL()}}/view/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['cleanname']}}.{{$unpaged_uploads[$i]['ext']}}"
				data-original-file="{{baseURL()}}/get/{{$unpaged_uploads[$i]['id']}}/{{$unpaged_uploads[$i]['originalname']}}.{{$unpaged_uploads[$i]['ext']}}"
			></a>
	@endfor
		</div>
@endif
@if($uploads->getLastPage() > 1)
		<div class="row text-center">
			{{$uploads->links()}}
		</div>
@endif
	</div>
	<div class="col-lg-3 col-md-2">
		<div class="hidden-sm hidden-xs">
			@include('includes.upload-sidebar')
		</div>
		<div class="row">
			<a href="{{baseURL()}}/{{(Request::is('collection/*') || Request::is('collections/*')) ? 'collection' : 'tag'}}/get/{{(Request::is('collection/*') || Request::is('collections/*')) ? $collection->name_unique : $collection->name}}" class="btn btn-lg btn-success btn-block" style="margin-bottom: 20px;"><span class="glyphicon glyphicon-arrow-down"></span> Download</a>
		</div>
		<div class="well well-sm row text-center" style="white-space: nowrap; overflow: hidden;">
			<p class="text-center">
				<b title="{{$collection->name}}">{{$collection->name}}</b>
			</p>
			<p style="font-size: 12px;">
@if(Request::is('collection/*') || Request::is('collections/*'))
				<b>Owner:</b> {{User::find($collection->user_id)->username}}<br>
@endif
				<b>Files:</b> {{count($collection->uploads)}}<br>
			</p>
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
	</div>
</div>
@endsection