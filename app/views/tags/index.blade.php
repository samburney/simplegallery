@extends('layouts.main')

@section('page_title')
	// Tags
@endsection

@section('content')
<div class="row">
	<div class="col-lg-10 col-md-10">
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
		<div class="text-center">
			{{$tags_paged->links()}}
		</div>
	</div>
	<div class="col-lg-2 col-md-2 hidden-sm hidden-xs">
		@include('includes.upload-sidebar')
	</div>
</div>
@endsection('content')