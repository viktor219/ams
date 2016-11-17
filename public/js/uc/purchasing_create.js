$(document).on('click', '.input_sr', function(event) {
	var e = $(this);
	var row = e[0].id.split('_')[1];
	$('#entryRow').val(row);
	$('#newModel').modal('toggle');
});
var _ploaded_models;
var _lastitem = 1;
//
//$.get(jsBaseUrl+"/ajaxrequest/loadmodels", function(data){
$.get(jsBaseUrl+"/public/autocomplete/json/purchasing/_models.json", function(data){
	_ploaded_models = data;
	$("#autocompleteitem_1").typeahead({ 
		onSelect: function(item) {
			//---- get model selected id
			$('#autocompletevalitem_1').val(item.value);
			$('#showRequestItem_1').attr('disabled', false);
			//---- show item in stock
			var url_3 = jsBaseUrl+"/ajaxrequest/checkstockavailable?model=" + item.value + "&customer=4";
			$.getJSON(url_3, function(data){
				$('#item-available-in-stock-1').html(data[0].stock + ' Available in Asset stock');
			});	
			//----
		},				
		source:data,
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
		autoSelect: true,
		items : 10	
	});
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
			newElem.find('.edit_item_button').attr('id', 'Edit_' + newNum + '');			
	//		
			newElem.find('#Edit_' + newNum).hide();
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
				
		newElem.find('.input_sr').attr('rel', 'tooltip_' + newNum + '');
 
	// insert the new element after the last "duplicatable" input field
		$('#entry' + num).after(newElem);
		newElem.find('.priceorder').blur();
	//
		$('#add-purchase-form').find('#vendor-group').hide();
 
	// enable the "remove" button
		$('#pbtnDel').attr('disabled', false);
		//
		$('#Edit_' + newNum).on('click', function(event) {
			var e = $(this);
			var row = e[0].id.split('_')[1];	
		});	
		//
		newElem.find('.select2-container').remove();
		//
		$('#showRequestItem_' + newNum).attr('disabled', true);
		//
		var customer = $("#autocomplete-customer").val();
		var url_models = jsBaseUrl+"/ajaxrequest/loadmodels?customer="+customer;
		//
		$('#autocompleteitem_' + newNum).typeahead({
			name: 'Models',
			onSelect: function(item) {
				$('#showRequestItem_' + newNum).attr('disabled', false);	
				$('#Edit_' + newNum ).show();
				$('#autocompletevalitem_' + newNum).val(item.value);
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
		//
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