@extends('layouts.main')

@section('content')
<div class="col-lg-10 col-md-10 col-xs-12">
	<h2>Search</h2>
{{Former::framework('TwitterBootstrap3')}}
{{Former::horizontal_open('search')}}
{{Former::text('q', 'Search Query', isset($q) ? $q : '')}}
{{Former::submit()}}
{{Former::close()}}
@if(isset($q))
	<h3>Results</h3>
	@if(count($uploads) || count($collections) || count($tags))
		<ul class="nav nav-tabs" id="SearchResultTabs" style="margin-bottom: 15px;">
		@if(count($uploads))
			<li class="active"><a href="#uploads" data-toggle="tab">Uploads</a></li>
		@endif
		@if(count($collections))
			<li><a href="#collections" data-toggle="tab">Collections</a></li>
		@endif
		@if(count($tags))
			<li><a href="#tags" data-toggle="tab">Tags</a></li>
		@endif
		</ul>
		<div class="tab-content">
			@if(count($uploads))
			<div class="tab-pane active" id="uploads">
				<? $uploads_arr = []; ?>
				@foreach ($uploads as $upload)
					<? $uploads_arr[] = $upload->toArray(); ?>
				@endforeach		
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
				<div class="text-center">{{$uploads->appends(array('q' => $q))->links()}}</div>
			</div>
			@endif
			@if(count($collections))
			<div class="tab-pane" id="collections">
				<? $uploads_arr = []; ?>
				@foreach ($collections as $collection)
					<? $uploads_arr[] = array('collection' => $collection, 'upload' => $collection->uploads()->first()->toArray()); ?>
				@endforeach		
				@for ($row=1; $row<=3; $row++)
						<div class="row thumbnail-row">
					@for ($col=1; $col<=4; $col++)
						<? $i = ($col + ($row - 1) * 4) - 1; ?>
						@if($i < count($uploads_arr))
							<div class="col-lg-3 col-md-3 col-sm-3 text-center">
								<div>
									<a href="{{baseURL()}}/collection/view/{{$uploads_arr[$i]['collection']['name_unique']}}">
										<img src="{{baseURL()}}/get/{{$uploads_arr[$i]['upload']['id']}}/{{$uploads_arr[$i]['upload']['cleanname']}}-768x576.jpg" class="img-thumbnail">
									</a>
								</div>
								<div style="text-align: center; white-space: nowrap; overflow: hidden;">
									<a href="{{baseURL()}}/collection/view/{{$uploads_arr[$i]['collection']['name_unique']}}" style="color: black;">
										<small>
											{{$uploads_arr[$i]['collection']['name']}}
										</small>
									</a>
								</div>
							</div>
						@endif			
					@endfor
						</div>
				@endfor
				<div class="text-center">{{$collections->appends(array('q' => $q))->links()}}</div>
			</div>
			@endif
			@if(count($tags))
			<div class="tab-pane" id="tags">
				@for ($row=1; $row<=3; $row++)
				<div class="row thumbnail-row">
					@for ($col=1; $col<=4; $col++)
						<? $i = ($col + ($row - 1) * 4) - 1; ?>
						@if($i < count($tags))
					<div class="col-lg-3 col-md-3 col-sm-3 text-center">
						<a href="{{baseURL()}}/tag/view/{{$tags[$i]['name']}}">
							<img src="{{baseURL()}}/get/{{$tags[$i]['uploads'][0]['id']}}/{{$tags[$i]['uploads'][0]['cleanname']}}-768x576.jpg" class="img-thumbnail">
						</a>
						<div style="text-align: center; white-space: nowrap; overflow: hidden;">
							<a href="{{baseURL()}}/tag/view/{{$tags[$i]['name']}}" style="color: black;">
								<small>
									{{$tags[$i]['name']}}
								</small>
							</a>
						</div>
					</div>
						@endif			
					@endfor
				</div>
				@endfor
			</div>
			@endif
			<div class="text-center">{{$tags->appends(array('q' => $q))->links()}}</div>		
		</div>
	@else
		No results found.
	@endif
@endif
</div>
<div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
	@include('includes.upload-sidebar')
</div>

@endsection