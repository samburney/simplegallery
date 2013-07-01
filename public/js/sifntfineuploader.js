var uploader;
var dragAndDrop;
var starttimes = [];

$(function(){
	uploader = new qq.FineUploaderBasic({
		button: $('#selectFilesToUpload')[0],
		debug: true,
		autoUpload: true,
		request: {
			endpoint: '/upload'
		},
		validation: {
			sizeLimit: 200 * 1024 * 1024 // 200MB
		},
		paste: {
			targetElement: $('#dropArea')[0]
		},
		callbacks: {
			onSubmit: function(id, fileName) {
				$('#uploadList')
					.append(
						$("<div>")
							.attr('id', 'upload-' + id)
							.css('overflow', 'hidden')
							.css('margin-top', '5px')
							.append(
								$('<div>')
									.addClass('progress')
									.attr('id', 'progress-' + id)
									.css('height', '6px')
									.css('margin-bottom', '0px')
									.append(
										$('<div>')
											.addClass('bar')
											.css('text-align', 'left')
											.css('color', 'black')
											.css('white-space', 'nowrap')
									)
							)
							.append(
								$('<small>')
									.html(fileName)
									.css('white-space', 'nowrap')
							)
							/*.append( // Remove disabled for now
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
							)*/
					)

				checkUploadList($('#uploadList'));
			},
			onPasteReceived: function(blob){
				var promise = new qq.Promise(),
					self = this;

				var fileName = prompt('Filename for Pasted Image', self._options.paste.defaultName);
				if(fileName){
					promise.success(fileName);
				}
				else{
					promise.failure();
				}

				return promise;
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

					$('#upload-' + id).prepend(
						$('<div>').append(
							$('<a>')
								.attr('href', '/view/' + data.file_id + '/' + data.file_name + '.' + data.file_ext)
								.addClass('thumbnail')
								.append(
									$('<img>')
										.attr('src', '/get/' + data.file_id + '/' + data.file_name + '-200x100.jpg')
								)
						)
					)
				}
				else{
					// TODO
				}
			},
			onError: function(id, name, reason, xhr){
				$('#progress-' + id).children('div.bar').css('width', '100%').addClass('bar-danger');
				$('#progress-' + id).click(function(){
					bootbox.alert(reason.message);
				});
			}
		}
	});

	$('#uploadNow').click(function(){
		uploader.uploadStoredFiles();
	});

	dragAndDrop = new qq.DragAndDrop({
		dropZoneElements: $('#dropArea'),
		callbacks: {
			processingDroppedFiles: function(){
				// TODO?
			},
			processingDroppedFilesComplete: function(files){
				// TODO? Hide stuff in above TODO
				uploader.addFiles(files);
			}
		}
	});
});

function checkUploadList(id){
	if(id.children('div').length > 0){
		$('#uploadList').show();
		//$('#uploadNow').show();
	}
	else{
		$('#uploadList').hide();
		//$('#uploadNow').hide();
	}
}