@section ('scripts')
	@parent
	<script type="text/javascript" src="{{asset('js/sifntfineuploader.js')}}"></script>
	<script type="text/javascript">
		$(function(){
			$('#collectionTabs a:first').tab('show');

			$('#addToExisting select').select2();
		});
	</script>
@endsection
<div class="well well-empty row">
	<div class="well-inner" style="text-align: center;">
		<a href="#" id="selectFilesToUpload" class="btn btn-primary btn-block">Select Files</a>
		<div id="dropArea" style="margin-top: 10px; border: 1px dashed grey;">
			<small>
				<b>
					Drop<br>
					or<br>
					Paste Here
				</b>
			</small>
		</div>
		<button id="showCollectionModal" class="btn btn-default btn-block hide" style="margin-top: 10px;">Add to Collection</button>
	</div>
	<div id="uploadList" class="well-inner" style="margin-top: 10px; text-align: center;" class="hide"></div>
	<div id="uploadNow" style="margin-top: 10px;" class="hide">
		<a href="#" class="btn btn-success">Go</a>
	</div>
</div>
<div id="collectionModal" class="modal fade">
	<div class="modal-dialog">
		<div class="modal-content">
			<div class="modal-header">
				<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
				<h3>Add to Collection</h3>
			</div>
			<div class="modal-body">
				<ul id="collectionTabs" class="nav nav-tabs">
					<li><a href="#createNew" class="active" data-toggle="tab">Create New</a></li>
@if(count($collection_list))
				<li><a href="#addToExisting" data-toggle="tab">Existing</a></li>			
@endif
				</ul>
				<div class="tab-content">
					<div class="tab-pane active" id="createNew">
						<form class="form-inline" style="margin-top: 20px;">
							Collection Name: <input type="text" name="collection_name" class="form-control" style="width: 200px;">
						</form>
					</div>
					<div class="tab-pane" id="addToExisting" style="height: 100px;">
						<h4>Add to Existing</h4>
						<select name="collection_id" style="width: 100%;">
@foreach($collection_list as $collection_option)
						<option value="{{$collection_option->id}}">{{$collection_option->name}}</option>
@endforeach
						</select>
					</div>			
				</div>
			</div>
			<div class="modal-footer">
				<button class="btn" data-dismiss="modal" aria-hidden="true">Close</button>
				<button class="btn btn-primary" id="addToCollection">Go</button>
			</div>
		</div>
	</div>
</div>