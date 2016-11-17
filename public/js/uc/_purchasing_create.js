$(document).on('click', '.input_sr', function(event) {
	var e = $(this);
	var row = e[0].id.split('_')[1];
	$('#entryRow').val(row);
	$('#newModel').modal('toggle');
});
var _ploaded_models;
var _lastitem = 1;
//
$.get(jsBaseUrl+"/ajaxrequest/loadmodels", function(data){
	_ploaded_models = data;
	$("#autocompleteitem_1").typeahead({ 
		onSelect: function(item) {
			//---- get model selected id
			$('#autocompletevalitem_1').val(item.value);
			//----
			/*//---- Remember pricing.
			var url_2 = jsBaseUrl+"/ajaxrequest/getpricing?customer=4&ordertype=" + $("#purchasetype").val() + "&idmodel=" + $('#autocompletevalitem_1').val();
			$.getJSON(url_2, function(data){
				$('#price_1').val(data[0].price);
			});		
			//----*/
			//---- show item in stock
			var url_3 = jsBaseUrl+"/ajaxrequest/checkstockavailable?model=" + item.value + "&customer=4";
			$.getJSON(url_3, function(data){
				$('#item-available-in-stock-1').html(data[0].stock + ' Available in Asset stock');
			});	
			//----
		},				
		source:data,
		autoSelect: true,
		items : 10	
	});
	//alert(data.toSource());
},'json');	
//
//submit purchase order.
$(document).ready(function () {
	//create order page form
	$('#submitPurchaseOrder').click(function (event) {
		$('.desc-group').removeClass('has-error');
		$('#vendor-group').removeClass('has-error');
		$('#estimatedtime-group').removeClass('has-error');
		$('#trackingnumber-group').removeClass('has-error');
		$('#shippingcompany-group').removeClass('has-error');
		$('.col-sm-2').removeClass('has-error');
		$('.col-sm-1').removeClass('has-error');
		$('.help-block').remove(); // remove the error text
	//
		//var vsalesordernumber = $('#salesordernumber').val();
		var vvendor = $('#add-purchase-form #selectVendor').val();
		var vestimatedtime = $('input[name=estimatedtime]').val();
		var vtrackingnumber = $('input[name=trackingnumber]').val();
		//var vshippingcompany = $('input[name=shippingcompany]').val();
		var vqty = $('.select_ttl').val();
		var vdescription = $('.input_fn').val();
		var vprice = $('.priceorder').val();
		var error = 0;
	//
		//if($('#vendor-group').is(':visible')) {
		//alert($('#requiredvendor').val());
		if($('#add-purchase-form #vendor-group').is(':visible')) {
			if (!vvendor) {
				$('#add-purchase-form #vendor-group').addClass('has-error'); // add the error class to show red input
				$('#add-purchase-form #vendor-group').append('<div class="help-block">Vendor field is required!</div>'); // add the actual error message under our input
				error++;
			}			
		}
		
		/*if (vestimatedtime.length == 0) {
			$('#estimatedtime-group').addClass('has-error'); // add the error class to show red input
			$('#estimatedtime-group').append('<div class="help-block">Estimated Time field is required!</div>'); // add the actual error message under our input
			error++;
		}

		if (vtrackingnumber.length == 0) {
			$('#trackingnumber-group').addClass('has-error'); // add the error class to show red input
			$('#trackingnumber-group').append('<div class="help-block">Tracking Number fiedl is required!</div>'); // add the actual error message under our input
			error++;
		}*/
		
		/*if (vshippingcompany.length == 0) {
			$('#shippingcompany-group').addClass('has-error'); // add the error class to show red input
			$('#shippingcompany-group').append('<div class="help-block">Shipping Company field is required!</div>'); // add the actual error message under our input
			error++;
		}*/

		if (vqty.length == 0) {
			$('.qty-group').addClass('has-error'); // add the error class to show red input
			$('.qty-group').append('<div class="help-block">Quantity is required!</div>'); // add the actual error message under our input
			error++;
		}
		
		if (vdescription.length == 0) {
			$('.desc-group').addClass('has-error'); // add the error class to show red input
			$('.desc-group').append('<div class="help-block">Item name is required!</div>'); // add the actual error message under our input
			error++;
		}
		
		if (vprice.length == 0) {
			$('.price-group').addClass('has-error'); // add the error class to show red input
			$('.price-group').append('<div class="help-block">Price is required!</div>'); // add the actual error message under our input
			error++;
		}
		
		//alert(error);
		if(!error)
			$('#add-purchase-form')[0].submit();
		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
		//
		return  false;
	});
	
});
$(function () {
			$(document).on('change', '.select_ttl', function() {
				$('#item-count-order').html('Total cost : $' + checkOrderAmount(false));
				//alert('changed');
			});

			$(document).on('change', '.price-group', function() {
				$('#item-count-order').html('Total cost : $' + checkOrderAmount(false));
				//alert('changed');
			});	
	//
	$('#pbtnAdd').click(function () {
		var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
	// manipulate the name/id values of the input inside the new element
			newElem.find('#package-option').attr('id', 'package-option' + newNum);
			newElem.find('#cleaning-options').attr('id', 'cleaning-options' + newNum);
			newElem.find('#testing-options').attr('id', 'testing-options' + newNum);
			newElem.find('.configuration-options').attr('id', 'configuration_options' + newNum).html('');
			//
			newElem.find('.edit_item_button').attr('id', 'Edit_' + newNum + '');			
			newElem.find('.comment_item_button').attr('id', 'Comment_' + newNum + '');			
			newElem.find('.comment').attr('id', 'itemNote_' + newNum + '');	
			
			newElem.find('#Edit_' + newNum).hide();
			newElem.find('#Comment_' + newNum).hide();
			newElem.find('#configOptions_' + newNum).hide();
			newElem.find('#itemNote_' + newNum).hide();			
			//newElem.find('#configuration_options').attr('id', 'configuration_options' + newNum);
			$('#item-count-order').html('Total cost : $' + checkOrderAmount(false));
		// Title - select
		newElem.find('.select_ttl').attr('id', 'quantity_' + newNum).attr('name', 'quantity[]').val('');
		
		newElem.find('.qty-group').removeClass( "has-success" );
		
		newElem.find('.itemqtystock').attr('id', 'item-available-in-stock-' + newNum).html('');
		
		newElem.find('.selectedItems').attr('id', 'item_s-' + newNum).attr('name', 'modelsid[]').val('');
		
		// First name - text
		newElem.find('.input_fn').attr('id', 'autocompleteitem_' + newNum).attr('name', 'description[]').val('').removeAttr('readonly');
		
		newElem.find('.priceorder').attr('id', 'price_' + newNum).attr('name', 'price[]').val('');
		
		newElem.find('.input_h').attr('id', 'autocompletevalitem_' + newNum).val('');
				
		newElem.find('.qty-group').removeClass('has-error');
		
		newElem.find('.desc-group').removeClass('has-error');
				
		newElem.find('.price-group').removeClass('has-error');
		
		newElem.find('.help-block').remove();
		
		newElem.find('.input_sr').attr('id', 'showRequestItem_' + newNum + '');
		
		newElem.find('.item_config_options_button').attr('id', 'configOptions_' + newNum + '');
		
		newElem.find('.input_sr').attr('rel', 'tooltip_' + newNum + '');
 
	// insert the new element after the last "duplicatable" input field
		$('#entry' + num).after(newElem);
		newElem.find('.priceorder').blur();
	//
		$('#add-purchase-form').find('#vendor-group').hide();
 
	// enable the "remove" button
		$('#pbtnDel').attr('disabled', false);
		newElem.find('.package_option').attr('id', 'package_option' + newNum + '').attr('name', 'package_option[' + newNum + '][]');
		newElem.find('.cleaning_option').attr('id', 'cleaning_option' + newNum + '').attr('name', 'cleaning_option[' + newNum + '][]');
		newElem.find('.testing_option').attr('id', 'testing_option' + newNum + '').attr('name', 'testing_option[' + newNum + '][]');
		newElem.find('.config_option').attr('id', 'config_option' + newNum + '').attr('name', 'config_option[' + newNum + '][]');
		$('#Edit_' + newNum).on('click', function(event) {
			var e = $(this);
			var row = e[0].id.split('_')[1];	
		});	
		$(document).on('click touchstart', '#Comment_' + newNum, function() {
			$('#itemNote_'+newNum).show();
		});	
		//		
		$('#autocompleteitem_' + newNum).on('change', function(){
			if($(this).val() !== "")
			{
				$('#configuration-options' + newNum).show();	
			}
			else 
				$('#configuration-options' + newNum).hide();	
		});
		//
		newElem.find('.select2-container').remove();
		//
		var customer = $("#autocomplete-customer").val();
		var url_models = jsBaseUrl+"/ajaxrequest/loadmodels?customer="+customer;
		//
		$('#autocompleteitem_' + newNum).typeahead({
			name: 'Models',
			onSelect: function(item) {
				if($("#purchasetype").val()==1){
					var url_1 = jsBaseUrl+"/modeloption/loadorderoption?id=" + item.value + "&entry_no=" + newNum;
					$.getJSON(url_1, function(data){
						//alert(data.toSource());
						if(data.html !== "")
							$('#entry' + newNum + ' .configuration-options').html('<h4><b>Configuration Options</b></h4>' + data.html + '');
					});	
				}else 
					$('#entry' + newNum + ' .configuration-options').html('');	
				$('#Comment_' + newNum ).show();
				$('#configOptions_' + newNum).show();
				$('#Edit_' + newNum ).show();
				$('#autocompletevalitem_' + newNum).val(item.value);
				//remember pricing.
				var url = jsBaseUrl+"/ajaxrequest/getpricing?customer=" + $("#customer_Id").val() + "&ordertype=" + $("#purchasetype").val() + "&idmodel=" + item.value;
				//alert(url);
				$.getJSON(url, function(data){
					if(data[0].price.length!==0)
						$('#price_' + newNum ).val(data[0].price);
					else 
						$('#price_' + newNum ).val('0.00');						
				});	
				//
				//show item in stock
				//alert(customerid);
				//---- show item in stock
				if($("#purchasetype").val()==1){//asset stock
					var url_3 = jsBaseUrl+"/ajaxrequest/checkstockavailable?model=" + item.value + "&customer=4";
					$.getJSON(url_3, function(data){
						$('#item-available-in-stock-' + newNum ).html(data[0].stock + ' Available in stock');
					});	
				}
			},				
			source:_ploaded_models,
			items : 10	
		});
		/*$('#autocompleteitem_' + newNum).typeahead({
			onSelect: function(item) {
				var url_1 = jsBaseUrl+"/modeloption/loadorderoption?id=" + item.value + "&entry_no=" + newNum;
				$.getJSON(url_1, function(data){
					//alert(data.toSource());
					$('#entry' + newNum).append('<div class="configuration-options row row-margin" id="configuration_options' + newNum + '"><h4><b>Configuration Options</b></h4>' + data.html + '</div>');
				});	
				//console.log(item);
				$('#autocompletevalitem_' + newNum).val(item.value);
				//remember pricing.
				var url = "/testing/live/ajaxrequest/getpricing?customer=" + $("#autocomplete-customer").val() + "&ordertype=" + $("#purchasetype").val() + "&idmodel=" + $('#autocompletevalitem_' + newNum).val();
				//alert(url);
				$.getJSON(url, function(data){
					$('#price_' + newNum ).val(data[0].price);
				});	
				//load item configuration 
				$.ajax({
					url: "<?php echo Yii::$app->request->baseUrl;?>/modeloption/ajaxcreate",
					data: {
						itemid : item.value
					},
					dataType: "json"
				}).done(function (data) {
					if (data.success) {
						$('configuration-options' + newNum).html(data.html);
					}
				});				
			},
			ajax: "/testing/live/ajaxrequest/listitem?query="+$('#autocompleteitem_' + newNum).val(),
			items : 10,
			matcher: function (item) {
					return true;
			},
			sorter: function (items) {
				return items.sort();
			}
		});			*/
		
		$('[rel="tooltip_' + newNum + '"]').tooltip();
	});
 
	$('#pbtnDel').click(function () {
	// confirmation
		var num = $('.clonedInput').length;
		var current_num = num -1;
	// how many "duplicatable" input fields we currently have
		$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
	// if only one element remains, disable the "remove" button
		if ( current_num === 1)
			$('#pbtnDel').attr('disabled', true);
	// enable the "add" button
		$('#pbtnAdd').attr('disabled', false).prop('value', "add section");});
	// enable the "add" button
	$('#item-count-order').html('Total cost : $' + checkOrderAmount(true));
		$('#pbtnAdd').attr('disabled', false);
		return false;
	});
 
	$('#pbtnDel').attr('disabled', true);
});

/*$(document).focusout( '.input_fn', function(e) {
	//var e = $(this);
	//var id = e.attr('id');
	//var row = id.split('_')[1];	
	var id = $('.input_fn:last').attr('id');
	var row = id.split('_')[1];	
	if($('#autocompletevalitem_'+row).val().length !== 0 )
	{
		$.ajax({
			url: jsBaseUrl+"/ajaxrequest/checkmodelpreferedvendor",
			data: {
				itemid : $('#autocompletevalitem_'+row).val() 
			},
			dataType: "json"
		}).done(function (data) {
			if (data.success) {
				var $preferedvendor = parseInt(data.preferedvendor);
				if($preferedvendor==1) {
					$('#add-purchase-form').find('#vendor-group').show();	
					$('#requiredvendor').val(1);
				}
				else {
					$('#add-purchase-form').find('#vendor-group').hide();
					$('#requiredvendor').val(0);
				}
			}
		});			
	}
});*/
//
$(document).on('click', '.edit_row', function(e) {
	$('.select_ttl').removeAttr('readonly');
	$('.input_fn').removeAttr('readonly');
	$('.priceorder').removeAttr('readonly');
	e.preventDefault();
});
//
$(document).on('change', '#selectPurchase', function() {
	var url = jsBaseUrl+"/ajaxrequest/getpurchasedetails?idpurchase=" + $(this).val();
	//alert(url);
	$.getJSON(url, function(data){
		$('#estimatedTime').val(data.estimated_time);
		$('#trackingnumber').val(data.trackingnumber);
		$('#shippingcompany').val(data.shipping_company);
		$('#shippingmethod').select2("val", data.shipping_deliverymethod);
	});
});