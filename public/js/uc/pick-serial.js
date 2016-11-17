/**
 * onchange plus file validation
 * extensions allowed : jpg|jpeg|png|gif
*/
$("#serialUnconvertedPicture").change(function(){
    var ext = $(this).val().split(".");
    ext = ext[ext.length-1].toLowerCase(); 
	var allowedExtensions = ['jpg', 'jpeg', 'png'];
	//
	if (allowedExtensions.lastIndexOf(ext) == -1) {
        //alert("Wrong extension type.");
        $("#pictureScanErrors").html("Wrong extension type. Extensions allowed : jpg, jpeg, png.");
        $("#serialUnconvertedPicture").val("");
    } else { //all looking fine!
		//$('#scan-picture-serial-form')[0].submit();
		//$("#scan-picture-serial-form").ajaxSubmit({url: jsBaseUrl+'/orders/default/scanserialpicture', type: 'post'});
		//alert($('#scan-picture-serial-form').serialize());
		/*var frm = $('#scan-picture-serial-form');
		frm.submit(function (ev) {
			$.ajax({
				type: frm.attr('method'),
				url: frm.attr('action'),
				data: frm.serialize(),
				success: function (data) {
					alert('ok');
				}
			});
			ev.preventDefault();
		});			
		frm[0].submit();*/
		var $form    = $('#scan-picture-serial-form'),
			formData = new FormData(),
			params   = $form.serializeArray(),
			files    = $form.find('[name="serialUnconvertedPicture"]')[0].files;		
		//
			formData.append('UploadForm[imageFiles]', files[0]);
		//
            $.each(params, function(i, val) {
                formData.append(val.name, val.value);
            });
		// 
            $.ajax({
                url: $form.attr('action'),
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                type: 'POST',
				dataType: "json",
				encode          : true,	
				beforeSend: function() { $('#loading').show();},
				complete: function() { $('#loading').hide();},				
                success: function(result) {
                    // Process the result ...
					//alert(result.toSource());
					$('#qserialnumber').val(result.message);
                }
            });		
	}
});
//first row edit button
$(document).on('click touchstart', '.edit_item_button', function(event) {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
	var model_id = $('.input_h').val();
	$.ajax({
			url: jsBaseUrl+"/orders/loadmodelform",
			type        : 'POST',
			data: {
				id: model_id,
				customerid: $("#customer_Id").val(),
				"_csrf":jsCrsf
			},
			dataType: "json",
			encode          : true,
			complete: function() {
				//load customers 
				$('#editModelCustomer_1').typeahead({
					onSelect: function(item) {
						$('#editModelCustomerval_1').val(item.value);
					},
					ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#editModelCustomer_1').val(),
					items : 10
				});						
				$('#update-model-form').attr('action', jsBaseUrl+"/orders/updatemodel?id="+model_id);
				//add button
				$('#PartbtnAdd').click(function () {
						var num     = $('.partClonedInput').length, // how many "duplicatable" input fields we currently have
							newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
							newElem = $('#partEntry' + num).clone().attr('id', 'partEntry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value							
						//alert(num);
					//
						newElem.find('.input_cust').attr('id', 'editModelCustomer_' + newNum).attr('name', 'editModelCustomer[]').val('');
						
						newElem.find('.input_partid').attr('id', 'partid_' + newNum).attr('name', 'partid[]').val('');
						
						newElem.find('.input_partdesc').attr('id', 'partdesc_' + newNum).attr('name', 'partdesc[]').val('');
					//
						newElem.find('.partdesc-group').removeClass('has-error');
					
						newElem.find('.customer-group').removeClass('has-error');
												
						newElem.find('.partid-group').removeClass('has-error');
					
						newElem.find('.help-block').remove();							
					//
						$('#partEntry' + num).after(newElem);
						$('#editModelCustomer_'+newNum).focus();
					//load customers 
					$('#editModelCustomer_'+newNum).typeahead({
						onSelect: function(item) {
							$('#editModelCustomerval_'+newNum).val(item.value);
						},
						ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#editModelCustomer_'+newNum).val(),
						items : 10
					});	
					// enable the "remove" button
						$('#PartbtnDel').attr('disabled', false);
					// right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
						if (newNum == 10)
						$('#PartbtnAdd').attr('disabled', true).prop('value', "You've reached the limit");								
				});
				//delete button
				$('#PartbtnDel').click(function () {
					// confirmation
						var num = $('.partClonedInput').length;
						var current_num = num -1;
					// how many "duplicatable" input fields we currently have
						$('#partEntry' + num).slideUp('fast', function () {$(this).remove(); 
					// if only one element remains, disable the "remove" button
						if ( current_num === 1)
							$('#PartbtnDel').attr('disabled', true);
					// enable the "add" button*
						$('#PartbtnAdd').attr('disabled', false).prop('value', "add section");});
					// remove the last element
				});
				//
				$('#PartbtnDel').attr('disabled', true);	
				//
				$('form#update-model-form').submit(function(event) {
					event.preventDefault(); // Prevent the form from submitting via the browser
					var form = $(this);
					$.ajax({
						type: 'POST',  
						url: form.attr('action'),
						data: form.serialize(),
						dataType: "json",
						encode          : true								 
					}).done(function(data) {
						form[0].reset();
						$('#model-update-msg').html(data.html);
						$('#model-update-msg').show();							
						$("#model-update-msg").delay(2000).fadeOut("slow", function () { 
							$("#model-update-msg").hide(); 
							$('#updateModel').modal('hide');
						});								
					});							
				});
			}
		}).done(function (data) {
			if (data.success) {
				$("#selectedModelName").html(data.itemname);
				$("#loadedModelForm").html(data.html);
				$('#updateModel').modal('show'); 
			}
		});
});