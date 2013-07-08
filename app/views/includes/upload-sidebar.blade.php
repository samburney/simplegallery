@section ('scripts')
	@parent
	<script type="text/javascript" src="{{asset('js/sifntfineuploader.js')}}"></script>
@endsection
<div class="well well-empty">
	<div class="well-inner" style="text-align: center;">
		<a href="#" id="selectFilesToUpload" class="btn btn-block">Select Files</a>
		<div id="dropArea" style="margin-top: 10px; border: 1px dashed grey;">
			<small>
				<b>
					Drop<br>
					or<br>
					Paste Here
				</b>
			</small>
		</div>
		<button id="createCollection" class="btn btn-block hide" style="margin-top: 10px;">New Collection</button>
	</div>
	<div id="uploadList" class="well-inner" style="margin-top: 10px; text-align: center;" class="hide"></div>
	<div id="uploadNow" style="margin-top: 10px;" class="hide">
		<a href="#" class="btn btn-success ">Go</a>
	</div>
</div>