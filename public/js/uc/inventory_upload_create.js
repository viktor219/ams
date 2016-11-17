$(document).ready(function() {
	$("#inventory-dropzone").dropzone({
		url: jsBaseUrl+"/ajaxrequest/uploadinventorypicture",
		addRemoveLinks: jsBaseUrl+"/inventory/removemodeluploaded",
		dictRemoveFile: "Delete",
		paramName: "files",
        dictDefaultMessage:  '<div class="jFiler-input-dragDrop"><div class="jFiler-input-inner"><div class="jFiler-input-icon"><i class="icon-jfi-cloud-up-o"></i></div><div class="jFiler-input-text"><h3>Drag&amp;Drop files here</h3> <span style="display:inline-block; margin: 6.5px 0">or</span></div><a class="jFiler-input-choose-btn blue">Browse Files</a></div></div>',
		init: function() {
			this.on('removedfile',function(file){
				var fileName = file.name;
				//alert(fileName);
				$.ajax({
					url: jsBaseUrl+"/inventory/removemodeluploaded?file="+fileName,
				});
			});
			//
			this.on('sending', function(file, xhr, formData){
				formData.append('_csrf', jsCrsf);
			});
			//prevent duplicates
			this.on("addedfile", function(file) {
				if (this.files.length) {
					var _i, _len;
					for (_i = 0, _len = this.files.length; _i < _len - 1; _i++) // -1 to exclude current file
					{
						if(this.files[_i].name === file.name && this.files[_i].size === file.size && this.files[_i].lastModifiedDate.toString() === file.lastModifiedDate.toString())
						{
							this.removeFile(file);
						}
					}
				}
			});
		}
	});
});