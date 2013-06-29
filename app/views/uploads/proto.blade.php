@extends('layouts.main')

@section('scripts')
	@parent

	<script type="text/javascript">
		var uploader;
		var starttimes = [];
		
		$(function(){
			uploader = new qq.FineUploaderBasic({
				button: $('#selectFilesToUpload')[0],
				debug: true,
				autoUpload: false,
				request: {
					endpoint: '/upload'
				},
				validation: {
					sizeLimit: 200 * 1024 * 1024 // 200MB
				},
				callbacks: {
					onSubmit: function(id, fileName) {
						$('#uploadList')
							.append(
								$("<div>")
									.attr('id', 'upload-' + id)
									.html(fileName + ' ')
									.append(
										$("<span>")
											.append(
											$("<a>")
												.click(function(){
													uploader.cancel(id);
													$(this).closest('div').remove();
													checkUploadList($('#uploadList'));
												})
												.attr('href', '#')
												.html('x')
												.addClass('btn btn-mini btn-danger')
												.addClass('remove')
											)
									)
									.append(
										$('<div>')
											.addClass('progress')
											.attr('id', 'progress-' + id)
											.css('height', '6px')
											.append(
												$('<div>')
													.addClass('bar')
													.css('text-align', 'left')
													.css('color', 'black')
													.css('white-space', 'nowrap')
											)
									)

							)

						checkUploadList($('#uploadList'));
					},
					onProgress: function(id, fileName, loaded, total){
						if(loaded < total){
							var date = new Date();

							if(!starttimes[id]){
								starttimes[id] = date.getTime() / 1000;
							}
							var progress = Math.round(loaded / total * 100);
							var speed = Math.round(loaded / ((date.getTime()/1000) - starttimes[id]));
							
							$('#progress-' + id).children('div.bar').css('width', progress + '%');
							//$('#progress-' + id).children('div.bar').html(loaded + '/' + total + ' ' + progress + '%' + ' ' + Math.round(speed/1024) + 'kB/s');
						}
					},
					onComplete: function(id, fileName, responseJSON){
						if(responseJSON.success){
							var data = responseJSON.data;

							$('#upload-' + id).find('a.remove').parent().remove();
							$('#progress-' + id).remove();

							$('#upload-' + id).append(
								$('<div>').append(
									$('<a>')
										.attr('href', '/view/' + data.file_id + '/' + data.file_name + '.' + data.file_ext)
										.append(
											$('<img>')
												.attr('src', '/get/' + data.file_id + '/' + data.file_name + '-200x100.jpg')
										)
								)
							)
						}
						else{
							// ERROR HANDLING TODO
						}
					},
					onError: function(id, name, reason, xhr){
						// ERROR HANDLING TODO
					}
				}
			});

			$('#uploadNow').click(function(){
				uploader.uploadStoredFiles();
			});
		});

		function checkUploadList(id){
			if(id.children('div').length > 0){
				$('#uploadNow').show();
			}
			else{
				$('#uploadNow').hide();
			}
		}
	</script>
@endsection('scripts')

@section('content')
<div class="row-fluid">
	<div class="span2">
		<a href="#" id="selectFilesToUpload" class="btn">Select Files</a>
		<a href="#" id="uploadNow" class="btn btn-success hide">Go</a>
		<div id="uploadList"></div>
	</div>
</div>
@endsection('content')