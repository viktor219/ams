$('#drag-and-drop-zone').dmUploader({
	url: jsBaseUrl+"/ajaxrequest/upload",
	dataType: 'json',
	allowedTypes: 'image/*',
	extFilter: 'jpg;png;gif',
	maxFiles: 1,
	extraData: {
	   '_csrf':jsCrsf
	},
	onInit: function(){
	  //$.danidemo.addLog('#demo-debug', 'default', 'Plugin initialized correctly');
	},
	onBeforeUpload: function(id){
	  //$.danidemo.addLog('#demo-debug', 'default', 'Starting the upload of #' + id);

	  $.danidemo.updateFileStatus(id, 'default', 'Uploading...');
	},
	onNewFile: function(id, file){
	  $('div[id^="demo-file"]').show();
	  $.danidemo.addFile('#demo-files', id, file);

	  /*** Begins Image preview loader ***/
	  if (typeof FileReader !== "undefined"){
		
		var reader = new FileReader();

		// Last image added
		var img = $('#demo-files').find('.demo-image-preview').eq(0);

		reader.onload = function (e) {
		  img.attr('src', e.target.result);
		}

		reader.readAsDataURL(file);

	  } else {
		// Hide/Remove all Images if FileReader isn't supported
		$('#demo-files').find('.demo-image-preview').remove();
	  }
	  /*** Ends Image preview loader ***/

	},
	onComplete: function(){
		//$.danidemo.addLog('#demo-debug', 'default', 'All pending tranfers completed');
	},
	onUploadProgress: function(id, percent){
		var percentStr = percent + '%';
		//$.danidemo.updateFileProgress(id, percentStr);
	},
	onUploadSuccess: function(id, data){
		//$.danidemo.addLog('#demo-debug', 'success', 'Upload of file #' + id + ' completed');
		//$.danidemo.addLog('#demo-debug', 'info', 'Server Response for file #' + id + ': ' + JSON.stringify(data));
		$.danidemo.updateFileStatus(id, 'success', 'Upload Complete');
		$.danidemo.updateFileProgress(id, '100%');
	},
	onUploadError: function(id, message){
		$.danidemo.updateFileStatus(id, 'error', message);
		//$.danidemo.addLog('#demo-debug', 'error', 'Failed to Upload file #' + id + ': ' + message);
	},
	onFileTypeError: function(file){
		//$.danidemo.addLog('#demo-debug', 'error', 'File \'' + file.name + '\' cannot be added: must be an image');
	},
	onFileSizeError: function(file){
		//$.danidemo.addLog('#demo-debug', 'error', 'File \'' + file.name + '\' cannot be added: size excess limit');
	},
	onFallbackMode: function(message){
		//$.danidemo.addLog('#demo-debug', 'info', 'Browser not supported(do something else here!): ' + message);
	}
});