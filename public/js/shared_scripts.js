function OpenReturnLabelModal(order)
{
   $.ajax({
		url: jsBaseUrl+"/orders/default/labeltest",
		beforeSend: function(){$('#loading').show();},
		data: {
			id: order
		},
		complete: function() {
			$('#loading').hide();
		},
		dataType: "json"
	}).done(function (data) { 
		if (data.success) {					    
			$('#loaded-return-label-content').html(data.html);
			$('#returnLabel').modal('show');
			$('#send-label-mail-button').attr('onClick', 'OpenLabelMail(' + order + ');');
			$('#download-label-mail-button').attr('href', data.filename);
			loadOrders("all", '', '');
		}
	});	
}

function OpenRelatedServiceModal(order)
{
	var isIE = window.ActiveXObject || "ActiveXObject" in window;
   $.ajax({
		url: jsBaseUrl+"/orders/default/relatedservice",
		beforeSend: function(){$('#loading').show();},
		data: {
			id: order
		},
		dataType: "json"
	}).done(function (data) { 
		if (data.success) {					    
			$('#service_number').html(data.order_number);
			$('#loaded-service-info-content').html(data.html);			    
			if (isIE) {
				$('#relatedServiceDetails').removeClass('fade');
			}
			$('#relatedServiceDetails').modal('show');
			$('#loading').hide();
		}
	});		
}

function openWarehouseModal(model)
{
   $.ajax({
		url: jsBaseUrl+"/inventory/default/warehouseform",
		beforeSend: function(){$('#loading').show();},
		data: {
			id: model 
		},
		dataType: "json",
		complete: function() {
			$('#loading').hide();
			//Loadclocations("", $('#selectWarehouseLocation'), 0, "Choose A Location");
			$('#selectWarehouseLocation').select2({placeholder: "Select an element", allowClear: true});
			//
			$('form#warehouse-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser
				var form = $(this);
				if(!$('#selectWarehouseLocation').val())
				{
					$('#order-location-group').addClass('has-error');
					new PNotify({
						title: 'Notifications',
						text: "Location should be choosen!",
						type: 'error',
						styling: "bootstrap3",
						opacity: 0.8,
						delay: 5000
					});				
				} else if($('#orderQty').val().length === 0) {
					$('#order-qty-group').addClass('has-error');
					new PNotify({
						title: 'Notifications',
						text: "Quantity of Order is required!",
						type: 'error',
						styling: "bootstrap3",
						opacity: 0.8,
						delay: 5000
					});						
				} else {
					$('#order-location-group').removeClass('has-error');
					$('#order-qty-group').removeClass('has-error');
					//
					$.ajax({
						type: 'POST',  
						url: jsBaseUrl+"/inventory/createwarehouseorder",
						data: form.serialize(),
						dataType: "json",
						encode : true,
						error: function (xhr, ajaxOptions, thrownError) {
							console.log(xhr.status);
							console.log(xhr.responseText);
							console.log(thrownError);
						}						
					}).done(function(data) {
						if(data.success)
						{
							form[0].reset();	
							$('#selectWarehouseLocation').select2('val', '');
							new PNotify({
								title: 'Notifications',
								text: 'Warehouse Order has been created successfully!',
								type: 'success',
								styling: "bootstrap3",
								opacity: 0.8,
								delay: 5000
							});		
						}

					});	
				}
			});
		}
	}).done(function (data) {
		if (data.success) {					    
			$('#modelname').html(data.itemname);
			$('#loaded-warehouse-content').html(data.html);
			$('#warehouseModal').modal('show');
		}
	});		
}

$('#applyowncustomerdetails').click(function () {
	if($("#applyowncustomerdetails").prop('checked'))
	{
		var url = jsBaseUrl+"/ajaxrequest/getmaincustomersettings";
		var main_account_number = null;
		var main_shipping_method = null; 
		$.getJSON(url, function(data){
			$("#defaultaccountnumber").val(data[0].accountnumber);
			$("#c1_shippingcompany").val(data[0].shippingcompany);
			loadShippingMethods($("#c1_shippingcompany"), $("#defaultshippingmethod"), data[0].shippingmethod);
			//
			//$("#defaultshippingmethod").val(data[0].shippingmethod);
		});						
	}
	else
	{
		$("#defaultaccountnumber").val("");
		$('#c1_shippingcompany').prop('selectedIndex', 0);
		$("#defaultshippingmethod").select2("val", "");				
	} 	
});	
//
$('.showreorder').on('click touchstart', function(event) {
	var id = $(this)[0].id;
	var arr = id.split('||');
	//alert (arr[0]);
	$('input[name=ritem_id]').val(arr[0]);
	$('input[name=rorder_id]').val(arr[3]);
	$('input[name=rmodel_id]').val(arr[4]);
	$('#r_item_name').html(arr[1]);
	$('#rqty').val(arr[2]);
	$('#ReOrder').modal('toggle');
});
//
$(document).on('keydown', '.input_fn', function(e) { 
	var keyCode = e.keyCode || e.which; 
	var e = $(this);
	//alert(e.closest('.clonedInput').find('.input_h').val());
	//
	if (keyCode == 9) { 
		if(e.closest('.clonedInput').find('.input_h').val().length !== 0) {	
			$("#btnAdd").click();
			//alert('tabbed!');
		}
		e.preventDefault(); 
		// call custom function here
	}
});

function loadShippingMethods(e, p, defaultSet)
{
	var items="";
	var url = jsBaseUrl+"/ajaxrequest/listshippingmethods?company=" + e.val(); 
	$.getJSON(url, function(data){  
		items+='<option value="">Select A Sipping method</option>';
		$.each(data,function(index,item){
			items+="<option value='"+item.id+"'>"+item.name+"</option>";
		});
		p.html(items); 
		if(defaultSet.length!==0){
			//alert(defaultSet);
			p.val(defaultSet).trigger('change');
		}
	}); 		
}
//autocomplete aei
$('#autocomplete-aei').typeahead({
	onSelect: function(item) {},
	ajax: jsBaseUrl+"/ajaxrequest/listaei?query="+$('#autocomplete-aei').val(),
	items : 5
});
//
$('#newmodel-form').submit(function(event) {
	var form = $(this);
	var row = $('#entryRow').val();
	var e = $('#autocompleteitem_'+row);
	//alert(form.serialize());
	$.ajax({
		type: 'POST',  
		url: jsBaseUrl+"/orders/addmodel",
		data: form.serialize(),
		dataType: "json",
		encode          : true								 
	}).done(function(data) {
		e.closest('.clonedInput').find('.comment_item_button').show();  
		e.closest('.clonedInput').find('.edit_item_button').show();
		$('#autocompleteitem_'+row).enable();
		$('#autocompleteitem_'+row).val($("#model_man option:selected").text() + ' ' + $('textarea#model_descrip').val());
		$('#autocompletevalitem_'+row).val(data.id);
		$('#newmodel-msg').html(data.html);
		form[0].reset();
		$('#newmodel-msg').show();							
		$("#newmodel-msg").delay(2000).fadeOut("slow", function () { 
			$("#newmodel-msg").hide(); 
			$('#newModel').modal('hide');
		});								
	});		
	event.preventDefault(); // Prevent the form from submitting via the browser
});
//
$('#send-mail-form').submit(function(event) {
	var form = $(this);
	var _type = parseInt($('input[name=type]').val());
	var orderId = $('input[name=orderId]').val();
	var qorderId = $('input[name=qorderId]').val();
	$.ajax({
		type: 'POST',  
		url: form.attr('action'),
		data: form.serialize(),
		dataType: "json",
		encode          : true								 
	}).done(function(data) {
		if(data.success) 
		{
			$('#sendemail-msg').html(data.message);
			if(_type==1)
			{
				$('#order-loaded-content-all #s-mail-button-'+orderId).removeClass('btn-primary');
				$('#order-loaded-content-all #s-mail-button-'+orderId).addClass('btn-success');
			}
			else if(_type==2)
			{
				//alert('#q-send-mail-button-'+qorderId);
				$('#order-loaded-content-quote #q-s-mail-button-'+qorderId).removeClass('btn-primary');
				$('#order-loaded-content-quote #q-s-mail-button-'+qorderId).addClass('btn-success');				
			}
			// $('#mail-button-'+orderId).removeAttr('onClick');
			// $('#mail-button-'+orderId).attr('data-poload', jsBaseUrl+'/ajaxrequest/getordermailstatus?idorder='+orderId);
			// $('#mail-button-'+orderId).attr('data-content', '');
			// $('#mail-button-'+orderId).attr('data-trigger', 'focus');
			// $('#mail-button-'+orderId).attr('role', 'button');
			// $('#mail-button-'+orderId).attr('data-toggle', 'popover');
			// $('#mail-button-'+orderId).attr('data-html', 'true');
			// $('#mail-button-'+orderId).attr('data-animation', 'true');
			// $('#mail-button-'+orderId).attr('rel', 'popover');
			// $('#mail-button-'+orderId).attr('title', 'Mail Reports');
			// $('#mail-button-'+orderId).attr('data-placement', 'left');		
			form[0].reset();
			/*$('#sendemail-msg').show();							
			$("#sendemail-msg").delay(2000).fadeOut("slow", function () { 
				$("#sendemail-msg").hide(); 
				
			});	*/
			new PNotify({
				title: 'Notifications',
				text: 'Mail has been successfully sent!',
				type: 'info',
				styling: "bootstrap3",
				opacity: 0.8,
				delay: 5000
			});			
			$('#sendEmail').modal('hide');
		}
	});		
	event.preventDefault(); // Prevent the form from submitting via the browser
});
//
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
//
$(document).on("click", '.showCustomer', function() {
	var e=$(this);
	//alert(e.attr('uid'));
	//loadCustomerDetails(e.attr('uid'));
	$.ajax({
		url: jsBaseUrl+"/customers/default/view",
		data: {
			id: e.attr('uid')
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#detaisOfCustomer").html(data.html);
			$("#customerDetails").modal('show');
		}
	});
});
//