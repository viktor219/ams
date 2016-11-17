//
function receivingCreate()
{
	$("#receivingCreateModal").modal('show');
}	
//
function LoadreceiveQtyModal(order, type)
{
	$.ajax({
		url: jsBaseUrl+"/receiving/receive",
		data: {
			ordernumber: order,
			type: type
		},
		dataType: "json",
		complete: function() {
			$('#SaveInStockQtyModal').click(function (event) {
				$('.help-block').remove(); // remove the error text
				var error = 0;
				$('.instockqty').each(function(i, obj) {
					$(this).parents("div").eq(0).removeClass('has-error');
					var vqty = $(this).val();
					
					if (vqty.length == 0) {
						$(this).parents("div").eq(0).addClass('has-error'); // add the error class to show red input
						$(this).parents("div").eq(0).append('<div class="help-block">Quantity field is required!</div>'); // add the actual error message under our input
						error++;
					} else if (vqty == 0){
						$(this).parents("div").eq(0).addClass('has-error'); // add the error class to show red input
						$(this).parents("div").eq(0).append('<div class="help-block">Quantity must be a correct value!</div>'); // add the actual error message under our input
						error++;					
					}				
				});
				if(!error) {
					if($('#add-instock-qty-form').find(".row").length > 0)
						$('#add-instock-qty-form')[0].submit();
					else 
						$("#ReceiveQtyDetails").modal('hide');
				}
				// stop the form from submitting the normal way and refreshing the page
				event.preventDefault();
				//
				return  false;				
			});			
		}
	}).done(function (data) {
		//alert(data.toSource());
		if (data.success) {
			//alert(data.html);
			$("#rec-title").html(data.title); 
			$("#detaisOfReceivingReceive").html(data.html); 
			$("#ReceiveQtyDetails").modal('show');
		} 
	});		
}
//
function saveSerializedReceivedItem(order, model, type)
{
	$('.col-md-12').removeClass('has-error'); 
	$('.help-block').remove(); // remove the error text
	
	var vcurrentserialgroup = $('#serial-group-'+ model +'');
	var vcurrentserialnumber = $('input[name=serialnumber_'+ model +']');
	var vserialnumber = vcurrentserialnumber.val();
	var vcurrentmodel = model;
	var type = type;

	vcurrentserialnumber.focus();
	if (vserialnumber.length == 0) {
		ion.sound.play("error");
		vcurrentserialgroup.addClass('has-error'); // add the error class to show red input
		vcurrentserialgroup.append('<div class="help-block">Serial Number field is required!</div>'); // add the actual error message under our input
	}else{
	//verify serial numbers...
	   $.ajax({
			url: jsBaseUrl+"/orders/default/validateserial",
			data: {
				"serial": vserialnumber,
				"currentmodel": vcurrentmodel,
			},
			dataType: "json",
			encode          : true
		}).done(function (data) {
			//alert(data.toSource());
			if(data.error) {
				ion.sound.play("error");
				vcurrentserialgroup.addClass('has-error'); // add the error class to show red input
				vcurrentserialgroup.append('<div class="help-block">' + data.html + '</div>'); // add the actual error message under our input									
			}else if(data.success){
				var formData = {
					"serial": vserialnumber,
					"order": order,
					"currentmodel": vcurrentmodel,
					"type": type,
					"_csrf":jsCrsf
				};
				//alert(formData.toSource());
				//save serial number...
				$.ajax({
					type        : 'POST',
					url: jsBaseUrl+"/receiving/default/savereceiveqtyserialized",
					data: formData,
					dataType: "json",
					encode          : true
				}).done(function (data) {
					//alert(data.toSource());
					if(data.success)
					{
						ion.sound.play("success");
						$('#add-instock-form-serialized')[0].reset();
						$('#serial-group-'+vcurrentmodel).removeClass('has-error');
						//change button status 
						if(data.done){
							//vcurrentserialgroup.hide(); 
							$('[name="serialnumber_'+vcurrentmodel+'"]').attr('disabled', true);
							$('#saveSerialBtn_'+vcurrentmodel).attr('disabled', true);
						}
						$('#serialized-quantity-instock-'+ vcurrentmodel +'').html(parseInt($('#serialized-quantity-instock-'+ vcurrentmodel).text())+1);
						$('#total-quantity-instock').html(parseInt($('#total-quantity-instock').text())+1);
						$('#serialized-quantity-received-'+ vcurrentmodel +'').html(parseInt($('#serialized-quantity-received-'+ vcurrentmodel).text())-1);
						//$('#total-quantity-received').html(parseInt($('#total-quantity-received').text())-1);
						$('#received-items-count').html(parseInt($('#received-items-count').text())-1);
						if($('#order-received-items-type').val()==1) {
							$('#so-received-items-count').html(parseInt($('#so-received-items-count').text())-1);
							$('#so-row-received-items-count-'+ vcurrentmodel).html(parseInt($('#so-row-received-items-count-'+ vcurrentmodel).text())-1);
						}
						else if($('#order-received-items-type').val()==2) {
							$('#po-received-items-count').html(parseInt($('#po-received-items-count').text())-1);
							$('#po-row-received-items-count-'+ vcurrentmodel).html(parseInt($('#po-row-received-items-count-'+ vcurrentmodel).text())-1);
						}
					}
				});		
			}
		});	
	}
}
//store_select2_single
$('#storenumber-group select').removeClass('store_select2_single');
$('#storenumber-group select').hide();
//
//if ($('[name="pushtoggle"]').is(':checked')){ 
$('input[name="returnstore"]').on('switchChange.bootstrapSwitch', function(event, state) {
	//alert(state); // true | false
	if(state) {
		$('#storenumber-group select').addClass('store_select2_single');
		$('#storenumber-group select').select2({width: '100%'});
		//$('#storenumber-group select').show();	
	} else {
		$('#storenumber-group select').removeClass('store_select2_single');
		$('#storenumber-group .select2-container').hide();		
	}	
});	
//
var __rloadeditems;
$('#receiving-customer').typeahead({
	onSelect: function(item) {
		$('#customer_Id').val(item.value);
		//
		var items="";
		customerid=item.value;
		//set default receiving location
		$.get(jsBaseUrl+"/ajaxrequest/getdefaultlocations?customerid="+customerid, function(data){
			$('#rselectLocation').select2("val", data.defaultreceivinglocation);
		},'json');
		Loadclocations($('#customer_Id').val(), $("#storenumber"), 0, "Returned From");
		$('.palletnumber-group').hide();
		$('.boxnumber-group').hide();
		/*//store number verification
		$.ajax({
			url: jsBaseUrl+"/ajaxrequest/verifycustomerstorenumberstatus?customerid="+customerid,
			dataType: "json"
		}).done(function (data) {
			if (data.success) {
				$('.storenumberinput').show();
			}				  
		});*/	
		//
		$("#autocompleteitem_1").typeahead('destroy');
		$.get(jsBaseUrl+"/public/autocomplete/json/receiving/"+customerid+"_models.json", function(data){
			__rloadeditems = data;
			//alert('hit');
			//autocomplete
			$("#autocompleteitem_1").typeahead({ 
				onSelect: function(item) {
					//---- get model selected id
					var modelid = item.value;
					$('#autocompletevalitem_1').val(modelid);
					$('#Comment_1').removeAttr('disabled');
					//pallet count verification
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomerpalletnumberstatus?customerid="+customerid,
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#uppallet_1').removeAttr('disabled');
							$('.palletnumber-group').show();
							$('.r_model-group').removeClass('col-sm-8');
							$('.r_model-group').addClass('col-sm-4');
						}				
					});		
					//box count verification
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomerboxnumberstatus?customerid="+customerid,
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#upbox_1').removeAttr('disabled');
							$('.boxnumber-group').show();
							$('.r_model-group').removeClass('col-sm-8');
							$('.r_model-group').addClass('col-sm-4');							
						}				
					});	
					//
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomermodelserialstatus?customerid="+customerid+"&modelid="+modelid,
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#entry1 .r_serialnumber').val(1);
							if($('#quantity_1').val().length == 0) {
								$('#entry1 .r_qty-group').addClass('has-error');
							} else {
								$('#entry1 .r_qty-group').removeClass('has-error');
								$('#Serial_1').enable();
								$(document).on('click touchstart', '#Serial_1', function() {
									openOSerialWindow(customerid, modelid, $('#quantity_1').val(), 1);
								});
							}
						} else if (data.errors)
							$('#entry1 .r_serialnumber').val(0);
					});				
					//----						
				},
                                sorter: function(items) {
                                    var beginswith = [],
                                    caseSensitive = [],
                                    caseInsensitive = [],
                                    item;
                                    while (item = items.shift()) {
                                        if (!item.name.toLowerCase().indexOf(this.query.toLowerCase()))
                                            beginswith.push(item);
                                        else if (~item.name.indexOf(this.query))
                                            caseSensitive.push(item);
                                        else
                                            caseInsensitive.push(item);
                                    }
                                    return beginswith.concat(caseSensitive, caseInsensitive);
                                },
				source:data,
				autoSelect: true,
				items : 10	
			}); 
		},'json');
	},
	ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#receiving-customer').val(),
	items : 10
});
$(document).on('click touchstart', '.next_up_pallet_button', function() {
	var e = $(this);
	var id = e.attr('id');
	var row = id.split('_')[1];
	var currentvalue = 1;
	//pallet count verification
	$.ajax({
		url: jsBaseUrl+"/ajaxrequest/verifycustomerpalletnumber?customerid="+customerid+"&modelid="+$('#autocompletevalitem_'+row).val(),
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			//alert(data.value);
			currentvalue = parseInt(data.value) + 1;
			$('#palletnumber_'+row).val(currentvalue);
		} else 
			$('#palletnumber_'+row).val(1);
		
	});	
});
//
$(document).on('click touchstart', '.next_up_box_button', function() {
	var e = $(this);
	var id = e.attr('id');
	var row = id.split('_')[1];
	//var currentvalue = parseInt($('#boxnumber_'+row).val());
	var currentvalue = 1;
	$.ajax({
		url: jsBaseUrl+"/ajaxrequest/verifycustomerboxnumber?customerid="+customerid+"&modelid="+$('#autocompletevalitem_'+row).val(),
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			//alert(data.value);
			currentvalue = parseInt(data.value) + 1;
			$('#boxnumber_'+row).val(currentvalue);
		} else 
			$('#boxnumber_'+row).val(1);
	});	
});	
//
$(document).ready(function () {
	//create order page form
	$('form#receive-unscheduled-inventory-form').submit(function(event) {
		$('#r_customer-group').removeClass('has-error');
		$('.r_model-group').removeClass('has-error');
		$('#r_location-group').removeClass('has-error');
		$('.r_qty-group').removeClass('has-error');
		$('#r_serialnumber-group').removeClass('has-error');
		$('.help-block').remove(); // remove the error text
		//
		var vcustomer = $('#receiving-customer').val();
		var vlocation = $('#rselectLocation').val();
		var qtys = $(".rquantity").children();
		var storenumberinputs = $(".storenumberinput").children();
		var palletnumbers = $(".palletnumber").children();
		var boxnumbers = $(".boxnumber").children();
		var items = $(".input_fn").children();		
		var vserial = $('input[name=receivingserialnumber]').val();
		var error = 0;
		//
		if (vcustomer.length == 0) {
			$('#r_customer-group').addClass('has-error'); // add the error class to show red input
			error++;
		} 
		if (!vlocation) {
			$('#r_location-group').addClass('has-error'); // add the error class to show red input
			error++;
		} 
		$(".r_serialnumber").each(function(i){
			var e = $(this);
			if(e.val()==1) { 
				var row = e[0].id.split('_')[1];
				//alert(row);
				$('#entry' + row + ' .r_model-group').addClass('has-error'); // add the error class to show red input
				$('#entry' + row + ' .r_model-group').append('<div class="help-block">Serial number is required!</div>'); // add the actual error message under our input
				error++;
			}
		});
		//
		$(".rquantity").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");
				$('#' + id).parents("div").eq(0).addClass( "has-error" ); // add the error class to show red input
				error++;
			}
		});
		//
		$(".input_h").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");
				$('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
				error++;
			}
		});		
		//
		/*$(".storenumberinput").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");			
				if($('#' + id).is(':visible'))
					$('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
				error++;
			}
		});	*/
		//
		$(".palletnumber").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");			
				if($('#' + id).is(':visible'))
					$('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
				error++;
			}
		});
		//
		$(".boxnumber").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");			
				if($('#' + id).is(':visible'))
					$('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
				error++;
			} 
		});
		//alert(error);
		//
		if(error) {
			event.preventDefault();
			//
			return false;
		}	
	}); 
});
//
$(function () {
	$('#btnRAdd').click(function () {
		var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
		var prevNum = newNum - 1;
		// manipulate the name/id values of the input inside the new element
			newElem.find('.edit_item_button').attr('id', 'Edit_' + newNum + '');			
			//newElem.find('.comment_item_button').attr('id', 'Comment_' + newNum + '');			
			newElem.find('.clear_item_button').attr('id', 'Clearbtn_' + newNum + '');			
			newElem.find('.r_serialnumber').attr('id', 'serialnumber_' + newNum + '').val(0);			
			//newElem.find('.storenumberinput').attr('id', 'storenumber_' + newNum + '').val('');			
			newElem.find('.next_up_pallet_button').attr('id', 'uppallet_' + newNum + '');			
			newElem.find('.next_up_box_button').attr('id', 'upbox_' + newNum + '');			
			newElem.find('.itemoption').attr('id', 'itemoption_' + newNum + '').val($('#itemoption_' + prevNum).val()).trigger('change');			
			newElem.find('.palletnumber').attr('id', 'palletnumber_' + newNum + '').val(0);			
			newElem.find('.boxnumber').attr('id', 'boxnumber_' + newNum + '').val(0);			
			newElem.find('.add_serial_button').attr('id', 'Serial_' + newNum + '');			
			//newElem.find('.comment').attr('id', 'itemNote_' + newNum + '');	
			newElem.find('.palletnumber-group').hide();
			newElem.find('.boxnumber-group').hide();
			
			newElem.find('#Edit_' + newNum).hide();
			newElem.find('.select2-container').remove();
			/*newElem.find('#itemoption_' + newNum).select2({
                    placeholder: "Select An Option",
					width: '100%',
                    allowClear: true
                });*/
			newElem.find('#Comment_' + newNum).attr('disabled', true);
			newElem.find('#Serial_' + newNum).attr('disabled', true);
			newElem.find('#uppallet_' + newNum).attr('disabled', true);
			newElem.find('#upbox_' + newNum).attr('disabled', true);
			//newElem.find('#itemNote_' + newNum).hide();
			//newElem.find('#configuration_options').attr('id', 'configuration_options' + newNum);
			$('#item-count-order').html('Total cost : $' + checkOrderAmount(false));
		// Title - select
		newElem.find('.rquantity').attr('id', 'quantity_' + newNum).attr('name', 'quantity[]').val('').removeAttr('disabled');
		
		newElem.find('.clear_serialized-group').hide();
		
		newElem.find('.qty-group').removeClass( "has-success" );
				
		newElem.find('.selectedItems').attr('id', 'item_s-' + newNum).attr('name', 'modelsid[]').val('');
 
		// First name - text
		newElem.find('.input_fn').attr('id', 'autocompleteitem_' + newNum).attr('name', 'description[]').val('').removeAttr('readonly').removeAttr('disabled');
				
		newElem.find('.input_h').attr('id', 'autocompletevalitem_' + newNum).val('');
		
		newElem.find('.qty-group').removeClass('has-error');
		
		newElem.find('.desc-group').removeClass('has-error');
				
		newElem.find('.help-block').remove();
 
	// insert the new element after the last "duplicatable" input field
		$('#entry' + num).after(newElem);
		//$('#ID' + newNum + '_title').focus();
 
	// enable the "remove" button
		$('#btnRDel').attr('disabled', false);
	//
		$('#Edit_' + newNum).on('click', function(event) {
			var e = $(this);
			var row = e[0].id.split('_')[1];	
			//alert(row);
			$('#Edit_' + row).hide();
			$('#entry'+row+' .input_fn').val('');
			$('#entry'+row+' .input_fn').removeAttr('readonly'); 
		});	
		//
		var url_models = jsBaseUrl+"/ajaxrequest/loadmodels";
		//$.get(url_models, function(data){
		//jhxhr.done(function(data){
			$('#autocompleteitem_' + newNum).typeahead({ 
				onSelect: function(item) {
					$('#Comment_' + newNum ).removeAttr('disabled');   					
					//$('#Comment_' + newNum ).show();
					$('#Edit_' + newNum ).show();
					//console.log(item);
					$('#autocompletevalitem_' + newNum).val(item.value);
					//
					//pallet count verification
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomerpalletnumberstatus?customerid="+$('#customer_Id').val(),
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#uppallet_' + newNum ).removeAttr('disabled');
							newElem.find('.palletnumber-group').show();
							$('.r_model-group').removeClass('col-sm-8');
							$('.r_model-group').addClass('col-sm-4');
						}				
					});		
					//box count verification
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomerboxnumberstatus?customerid="+$('#customer_Id').val(),
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#upbox_' + newNum ).removeAttr('disabled');
							newElem.find('.boxnumber-group').show();
							$('.r_model-group').removeClass('col-sm-8');
							$('.r_model-group').addClass('col-sm-4');
						}				
					});	
					//$('#autocompleteitem_' + newNum).attr('readonly', 'readonly');
					//
					/*$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomermodelserialstatus?customerid="+$('#customerId').val()+"&modelid="+item.value,
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#entry' + newNum + ' .r_serialnumber-group').html('<input type="text" class="rec_serial" class="form-control" name="receivingserialnumber[' + item.value + '][]" placeholder="Enter Serial Numbers"/>');
						} else if(data.errors) {
							$('#entry' + newNum + ' .r_serialnumber-group').html('');
						}				
					});	*/
					$.ajax({
						url: jsBaseUrl+"/ajaxrequest/verifycustomermodelserialstatus?customerid="+$('#customer_Id').val()+"&modelid="+item.value,
						dataType: "json"
					}).done(function (data) {
						if (data.success) {
							$('#entry' + newNum + ' .r_serialnumber').val(1);	
							if($('#quantity_' + newNum ).val().length == 0) {
								$('#entry' + newNum + ' .r_qty-group').addClass('has-error');
							}
							else {
								$('#entry' + newNum + ' .r_qty-group').removeClass('has-error');																
								$('#Serial_' + newNum).removeAttr('disabled'); 
								$(document).on('click touchstart', '#Serial_' + newNum, function() {
									openOSerialWindow(customerid, item.value, $('#quantity_' + newNum).val(), newNum);
								});
							}
						} else if(data.errors)
							$('#entry' + newNum + ' .r_serialnumber').val(0);
					});						
				},				
				source:__rloadeditems,
				autoSelect: true,
				items : 10	
			});
		//});
		//},'json');
	});
 
	$('#btnRDel').click(function () {
	// confirmation
		var num = $('.clonedInput').length;
		var current_num = num -1;
	// how many "duplicatable" input fields we currently have
		$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
	// if only one element remains, disable the "remove" button
		if ( current_num === 1)
			$('#btnRDel').attr('disabled', true);
	// enable the "add" button
		$('#btnRAdd').attr('disabled', false).prop('value', "add section");});
	});
});
//
$(document).on("input", "#detaisOfReceivingReceive input[type='number']", function() {
    this.value = this.value.replace(/[^0-9\.]/g,'');
});
//
$(document).on("keyup", "#detaisOfReceivingReceive input[type='number']", function() {
    var max = parseInt($(this).attr('max'));
	var quantity_entered = parseInt($(this).val());
	if(quantity_entered > max)
		$(this).parents("div").eq(0).addClass('has-error');
	else 
		$(this).parents("div").eq(0).removeClass('has-error');
});
//
function ViewOrderDetails(id)
{
	$.ajax({
		url: jsBaseUrl+"/orders/rview",
		data: {
			id: id
		},
		dataType: "json",
		complete: function() {
			//
			//$('input[name=_csrf]').val(jsCrsf);
			//
			/*$('#SavePReceiveQtyModal').click(function (event) {
				$('.help-block').remove(); // remove the error text
				var error = 0;
				$('.purchasingqty').each(function(i, obj) {
					$(this).parents("div").eq(0).removeClass('has-error');
					var vqty = $(this).val();
					
					if (vqty.length == 0) {
						$(this).parents("div").eq(0).addClass('has-error'); // add the error class to show red input
						$(this).parents("div").eq(0).append('<div class="help-block">Quantity field is required!</div>'); // add the actual error message under our input
						error++;
					} else if (vqty == 0){
						$(this).parents("div").eq(0).addClass('has-error'); // add the error class to show red input
						$(this).parents("div").eq(0).append('<div class="help-block">Quantity must be a correct value!</div>'); // add the actual error message under our input
						error++;					
					}				
				});
				if(!error) {
					//if(confirm("Are you sure that the following items have arrived?")==true)
						$('#add-receive-qty-form')[0].submit();
				}
				// stop the form from submitting the normal way and refreshing the page
				event.preventDefault();
				//
				return  false;				
			});*/			
		}
	}).done(function (data) {
		if (data.success) {
			$("#number_generated").html(data.title);
			$("#detaisOfPurchasing").html(data.html);
			//$("#purchasingDetails").modal('show');
			$("#ReceivePQtyDetails").modal('show');
			$('#SaveReceiveQtyModal').click(function (event) {
				window.location =  jsBaseUrl+"/orders/savereceiveqty?id="+data.id;
				// stop the form from submitting the normal way and refreshing the page
				event.preventDefault();
				//
				return  false;				
			});
		}
	});	
}
//
$(document).on("click", '.viewCustomer', function() {
	var e=$(this);
	//alert(e.attr('uid'));
	//loadCustomerDetails(e.attr('uid'));
	$.ajax({
		url: jsBaseUrl+"/customers/default/view",
		data: {
			id: e.attr('cid')
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
$('form#add-receiving-serial-form').submit(function(event) {

	$(this).find('.col-md-12').removeClass('has-error'); 
	$(this).find('.help-block').remove(); // remove the error text
	
	var vserialnumber = $('input[name=serialnumber]').val();
	var vcurrentmodel = $('#serialCurrentModel').val();
	var vquantity = $('#serialQuantity').val();
	var vcustomer = $('#customerId').val();
	var triggerRow = $('#triggerRow').val();

	if (vserialnumber.length == 0) {
		$('#qserialnumber').focus();
		ion.sound.play("error");
		$('#serial-group').addClass('has-error'); // add the error class to show red input
		$('#serial-group').append('<div class="help-block">Serial Number field is required!</div>'); // add the actual error message under our input
	}else{
		$('#qserialnumber').focus();
	//verify serial numbers...
	   $.ajax({
			url: jsBaseUrl+"/orders/default/validateserial",
			data: {
				"serial": vserialnumber,
				"currentmodel": vcurrentmodel,
			},
			dataType: "json",
			encode          : true
		}).done(function (data) {
			//alert(data.toSource());
			if(data.error) {
				$('#qserialnumber').focus();
				ion.sound.play("error");
				$('#serial-group').addClass('has-error'); // add the error class to show red input
				$('#serial-group').append('<div class="help-block">' + data.html + '</div>'); // add the actual error message under our input									
			}else if(data.success){
				$('#qserialnumber').focus();
				//save serial number...
				$.ajax({
					type        : 'POST',
					url: jsBaseUrl+"/receiving/default/saveserial",
					data: {
						"serial": vserialnumber,
						"currentmodel": vcurrentmodel,
						"quantity": vquantity,
						"customerId": vcustomer,
						"_csrf":jsCrsf
					},
					dataType: "json",
					encode          : true
				}).done(function (data) {
					if(data.success)
					{
						$('#qserialnumber').focus();
						ion.sound.play("success");
						$('#add-receiving-serial-form')[0].reset();
						//change button status 
						if(data.done){
							$('#addSerials').modal('hide'); 
							$('#entry'+triggerRow+' .r_serialnumber').val(0);
							$('#entry' + triggerRow + ' .r_model-group').removeClass('has-error');
							$('#entry' + triggerRow + ' .r_model-group').find('.help-block').remove();
							$('#entry'+triggerRow+' .rquantity').attr('disabled', true);
							$('#entry'+triggerRow+' .input_fn').attr('disabled', true);
							$('#entry'+triggerRow+' .add_serial_button').attr('disabled', true);
							//$('#entry'+triggerRow+' .input_h').val(0);
							$('#entry'+triggerRow+' .clear_serialized-group').show();
						}else{ 
							loadReceivingSerializedNextModel(vcurrentmodel, vcustomer, vquantity, data.current_quantity, triggerRow);
						}								
					}
				});		
			}
		});
	}

	// stop the form from submitting the normal way and refreshing the page
	event.preventDefault();
	//
	return  false;
});
//
$('#addSerials').on('shown.bs.modal', function() {
	$('#qserialnumber').focus();
})