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

@section('content')
<div class="row-fluid">
	<div class="span9">
<? $uploads_arr = []; ?>
@foreach($uploads as $upload)
<? $uploads_arr[] = $upload->toArray(); ?>
		<div id="upload-{{$upload->id}}" style="margin-bottom: 10px;">		
			<a href="/view/{{$upload->id}}/{{$upload->cleanname}}.{{$upload->ext}}">
				<img src="/get/{{$upload->id}}/{{$upload->cleanname}}-710.{{$upload->ext}}" class="img-polaroid">
			</a>
		</div>
@endforeach
	</div>
	<div class="span3">
		<div class="row-fluid">
			<div class="well well-small">
				<p class="text-center">
					<b>{{$collection->name}}</b>
				</p>
				<p style="font-size: 12px;">
					<b>Owner:</b> {{User::find($collection->user_id)->username}}<br>
					<b>Files:</b> {{count($collection->uploads)}}<br>
				</p>
			</div>
		</div>
		<div class="row-fluid">
			<div class="well well-small text-center">
@for ($row=1; $row<=3; $row++)
	@if(count($uploads_arr) / ($row * 3) >= 1)
				<ul class="thumbnails thumbnails-tiny">
		@for ($col=1; $col<=3; $col++)
			<? $i = ($col + ($row - 1) * 3) - 1; ?>
			@if($i < count($uploads_arr))
					<li class="span4">
						<div class="image-thumbnail">
							<a href="#upload-{{$uploads_arr[$i]['id']}}">
								<img src="/get/{{$uploads_arr[$i]['id']}}/{{$uploads_arr[$i]['cleanname']}}-60x60.jpg" class="img-polaroid img-polaroid-tiny">
							</a>
						</div>
					</li>
			@endif			
		@endfor
				</ul>
	@endif
@endfor
				<div class="pagination-mini" style="margin-top: -10px; margin-bottom: -20px;">
					{{$uploads->links()}}
				</div>
			</div>
		</div>
@include('uploads.upload-sidebar')
	</div>
</div>
@endsection