var uploader;
var dragAndDrop;
var starttimes = [];
var collection = [];
var activeUploads = 0;
var upload_ids;

$(function(){
	uploader = new qq.FineUploaderBasic({
		button: $('#selectFilesToUpload')[0],
		debug: true,
		autoUpload: true,
		request: {
			endpoint: baseURL + '/upload'
		},
		validation: {
			sizeLimit: 200 * 1024 * 1024 // 200MB
		},
		paste: {
			targetElement: $('#dropArea')[0]
		},
		callbacks: {
			onSubmit: function(id, fileName) {
				activeUploads++;
				console.log(activeUploads);
				$('#showCollectionModal').attr('disabled', 'disabled').addClass('disabled');

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
											.addClass('progress-bar')
											.css('text-align', 'left')
											.css('color', 'black')
											.css('white-space', 'nowrap')
									)
							)
							.append(
								$('<span>')
									.html(fileName)
									.attr('style', 'font-size: 10px;')
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
					
					console.log(progress);
					$('#progress-' + id).children('div.progress-bar').css('width', progress + '%');
					//$('#progress-' + id).children('div.progress-bar').html(loaded + '/' + total + ' ' + progress + '%' + ' ' + Math.round(speed/1024) + 'kB/s');
				}
			},
			onComplete: function(id, fileName, responseJSON){
				if(responseJSON.success){
					var data = responseJSON.data;

					activeUploads--;
					collection[collection.length] = data.file_id;
					console.log(activeUploads);
					if(activeUploads <= 0){
						$('#showCollectionModal').show().removeClass('hide').removeAttr('disabled').removeClass('disabled');
					}

					$('#upload-' + id).find('a.remove').parent().remove();
					$('#progress-' + id).remove();

					$('#upload-' + id).prepend(
						$('<div>').append(
							$('<a>')
								.attr('href', baseURL + '/view/' + data.file_id + '/' + data.file_name + '.' + data.file_ext)
								.append(
									$('<img>')
										.attr('src', baseURL + '/get/' + data.file_id + '/' + data.file_name + '-108x100.jpg')
										.addClass('img-thumbnail')
								)
						)
					)
				}
				else{
					// TODO
				}
			},
			onError: function(id, name, reason, xhr){
				$('#progress-' + id).children('div.progress-bar').css('width', '100%').addClass('progress-bar-danger');
					$('#progress-' + id).click(function(){
						//bootbox.alert(reason.message);
						alert(reason);
					});
				}
			}
		});

		$('#uploadNow').click(function(){
			uploader.uploadStoredFiles();
		});

		$('#showCollectionModal').click(function(){
			upload_ids = collection;

			$('#collectionModal').modal();
		});

		$('#addToCollection').click(function(){
			if($('#createNew').hasClass('active')){
				var name = $('#createNew').find('input:text[name=collection_name]').val();

				$.post(
					baseURL + '/collection/new',
					{
						ids: upload_ids,
						name: name,
					},
					function(data){
						if(data.success){
								window.location = baseURL + '/collection/view/' + data.name_unique;
						}
					},
					'json'
				);
			}
			else if($('#addToExisting').hasClass('active')){
				var collection_id = $('#addToExisting').find('select[name=collection_id]').val();

				$.post(
					baseURL + '/collection/addtoexisting',
					{
						ids: upload_ids,
						collection_id: collection_id,
					},
					function(data){
						if(data.success){
								window.location = baseURL + '/collection/view/' + data.name_unique;
						}
					},
					'json'
				);
			}
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
		$('#uploadList').show().removeClass('hide');
		//$('#uploadNow').show();
	}
	else{
		$('#uploadList').hide();
		//$('#uploadNow').hide();
	}
}
