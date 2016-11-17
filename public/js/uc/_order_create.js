/***
 * order_create.js 
 * All script executed in order create form.
 */
//request item button at first row.
$(document).on('click', '.input_sr', function(event) {
	//$('#requestItem').modal('toggle');
	var e = $(this);
	var row = e[0].id.split('_')[1];
	$('#entryRow').val(row);
	$('#newModel').modal('toggle');
});
//
$("#selectLocation").change(function() {
	var e = $(this);
	var locationid = e.val();
	if(locationid.length > 0)
		loadlocationotherdetails($('#add-order-form #shippingcompany'), $('#add-order-form #accountnumber'), $("#add-order-form #shippingmethod"), e);
	/*if(!$(this).val()) {
		var url = jsBaseUrl+"/ajaxrequest/getshippingotherdetailsfromlocation?locationid="+locationid;
		$.getJSON(url, function(data){
			$('#accountnumber').val(data.s1);
			$('#shippingcompany').val(data.s2);
			loadShippingMethods($("#shippingcompany"), $("#shippingmethod"), data.s3);
			$("#switch-shipping-detail-tab button").removeClass('active');
			$('#switch-shipping-detail-tab button').each(function(){
				if(parseInt($(this).data('switch-value'))==2)
					$(this).addClass('active');
			});				
		});	
	}*/
});

$("#purchasetype").change(function() {
	var type = $(this).val();
	var today = 24*60*60*1000;
	var date = null;
	//clear items rows 
	$('.select_ttl').each(function() {
		$(this).val("");
	});
	$('.input_fn').each(function() {
		$(this).val("");
	});
	$('.input_h').each(function() {
		$(this).val("");
	});	
	$('.selectedItems').each(function() {
		$(this).val("");
	});	
	$('.priceorder').each(function() {
		$(this).val("");
	});				
	if(type==1){
		$(".shipbydatef").val(shipByPurchaseDate);
		$('.warehousing-panel').hide(); 
		$('.input_fn').show();  
		$('#autocomplete-shipment-type').val("");
		$('#switch-shipment-type').hide();					
		$('.selectedItems').each(function() {
			$(this).removeClass("item_select2_single");
			$(this).closest('.clonedInput').find('.select2-container').remove();
		});			
		$("#switch-shipment-type button").removeClass('active');
	}
	else if(type==2){
		$(".shipbydatef").val(shipByServiceDate);
		$('.warehousing-panel').hide();
		$('.input_fn').show();
		$('#autocomplete-shipment-type').val("");
		$('#switch-shipment-type').hide();					
		$('.selectedItems').each(function() {
			$(this).removeClass("item_select2_single");
			$(this).closest('.clonedInput').find('.select2-container').remove();
		});					
		$("#switch-shipment-type button").removeClass('active');
	}
	else if(type==3){
		$(".shipbydatef").val(shipByIntegrationDate);
		$('.warehousing-panel').hide(); 
		$('.input_fn').hide();
		$('#switch-shipment-type').show();
		$('.selectedItems').each(function() {
			$(this).addClass('item_select2_single');
			$(this).select2({width: '100%'});
			$(this).closest('.clonedInput').find('.select2-container').show();
		});
	}
	else if(type==4)
	{
		$(".shipbydatef").val(shipByWarehouseDate);
		$('.warehousing-panel').show();
		$('.input_fn').hide();
		$('#autocomplete-shipment-type').val("");
		$('#switch-shipment-type').hide();
		$('.selectedItems').each(function() {
			$(this).addClass('item_select2_single');
			$(this).select2({width: '100%'});
			$(this).closest('.clonedInput').find('.select2-container').show();
		});		
		$("#switch-shipment-type button").removeClass('active');
	}
});
//TODO : move to order.js + receiving.js : add autocomplete-customer typeahead
var customerid;
var _loadeditems;
$(function () {
	$(document).on("click touchstart", "#optype-group .btn", function(){                 
		$("#optype-group").button('reset');				  
	});						
	$(document).on("click touchstart", "#switch-order-type [data-switch-set]", function() {
		$("#switch-order-type button").removeClass('active');
		$(this).addClass('active');
		$("#switch-order-type button").css({"background-color": "", "color": "", "box-shadow": ""});
		$(this).css({"background-color": "#26B99A", "color": "#FFF", "box-shadow": "inset 0px 0px 5px #BBB"});
		$("#purchasetype").val($(this).data("switch-value")).trigger('change');
	});
	$(document).on("click touchstart", "#switch-shipment-type [data-switch-set]", function() {
		$("#switch-shipment-type button").removeClass('active');
		$(this).addClass('active');
		$("#switch-shipment-type button").css({"background-color": "", "color": "", "box-shadow": ""});
		$(this).css({"background-color": "#26B99A", "color": "#FFF", "box-shadow": "inset 0px 0px 5px #BBB"});
		$("#autocomplete-shipment-type").val($(this).data("switch-set-value"));
	});	
	$(document).on("click touchstart", "#switch-shipping-detail-tab [data-switch-set]", function() {
		$("#switch-shipping-detail-tab button").removeClass('active');
		$(this).addClass('active');
		var value = parseInt($(this).data("switch-value"));
		var customerid = $("#customer_Id").val();
		//enable fields
		$('#accountnumber').prop('disabled', false);
		$('#shippingcompany').prop('disabled', false);
		$("#shippingmethod").prop('disabled', false);
		//clear old entries
		$('#accountnumber').val("");
		$('#shippingcompany').prop('selectedIndex', 0);
		$("#shippingmethod").select2("val", "");						
		//alert(value);
		if(value==0) //Load asset settings 
		{
			var url = jsBaseUrl+"/ajaxrequest/getshippingassetdetails";
			$.getJSON(url, function(data){
				$('#accountnumber').val(data.s1);
				$('#shippingcompany').val(data.s2);
				if($("#shippingcompany").val().length > 0)
					loadShippingMethods($("#shippingcompany"), $("#shippingmethod"), data.s3);
			});								
		}
		else if(value==1) //load main settings for customer.
		{
			var url = jsBaseUrl+"/ajaxrequest/getshippingmaindetails?customer=" + customerid;
			$.getJSON(url, function(data){
				$('#accountnumber').val(data.s1);
				$('#shippingcompany').val(data.s2);
				if($("#shippingcompany").val().length > 0)
					loadShippingMethods($("#shippingcompany"), $("#shippingmethod"), data.s3);
			});									
		}
		else if(value==2)
		{							
			if(!customerid)
			{
				alert('Customer must be choosen before');
				$(this).removeClass('active');
			}
			else //Load customer settings 
			{
				var url = jsBaseUrl+"/ajaxrequest/getshippingotherdetails?customer=" + customerid;
				$.getJSON(url, function(data){
					$('#accountnumber').val(data.s1);
					$('#shippingcompany').val(data.s2);
					if($("#shippingcompany").val().length > 0)
						loadShippingMethods($("#shippingcompany"), $("#shippingmethod"), data.s3);
				});	
			}
		}
	});	
	//
	$('#autocomplete-customer').typeahead({
		onSelect: function(item) {
			var items="";
			customerid=item.value;
			$('#customer_Id').val(customerid);
			//load shipments settings 
			var url = jsBaseUrl+"/ajaxrequest/getshippingmaindetails?customer=" + customerid;
			$.getJSON(url, function(data){
				//set customer button 
				$('#switch-shipping-detail-tab button').each(function(){
					if(parseInt($(this).data('switch-value'))==1)
						$(this).addClass('active');
				});				
				//enable fields
				$('#accountnumber').prop('disabled', false);
				$('#shippingcompany').prop('disabled', false);
				$("#shippingmethod").prop('disabled', false);			
				//set
				$('#accountnumber').val(data.s1);
				$('#shippingcompany').val(data.s2);
				//if($("#shippingcompany").val().length > 0)
				loadShippingMethods($("#shippingcompany"), $("#shippingmethod"), data.s3);
			})
			//
			var url = jsBaseUrl+"/ajaxrequest/listorderlocations?customer=" + customerid;
			$("#selectLocation").removeAttr('disabled'); 
			//load location items for customer
			$("#selectLocation").select2("val", "");	
			Loadlocations($('#customer_Id').val(), $("#selectLocation"), 0);
			//remember last order type 
			var url = jsBaseUrl+"/ajaxrequest/getlastordertype?customer=" + customerid;
			$.getJSON(url, function(data){
				/*var _defaultordertype = data[0]['ordertype'];
				      $("#purchasetype option").each(function (a, b) {
							if ($(this).val() == _defaultordertype ) $(this).attr("selected", "selected");
						});*/
				//alert(_defaultordertype);
				//$("#purchasetype").val(_defaultordertype).trigger('change');
				//$("#purchasetype  option[value=" + _defaultordertype + "]").prop("selected", true);
				$('#switch-order-type button[data-switch-value="'+data[0]['ordertype']+'"]').click();
				$("#switch-order-type button").css({"background-color": "", "color": "", "box-shadow": ""});
				$('#switch-order-type button[data-switch-value="'+data[0]['ordertype']+'"]').css({"background-color": "#26B99A", "color": "#FFF", "box-shadow": "inset 0px 0px 5px #BBB"});
				if(data[0]['ordertype']==1){
					$(".shipbydatef").val(shipByPurchaseDate);
					$('.warehousing-panel').hide();
					$('.input_fn').show();
					$('#autocomplete-shipment-type').val("");
					$('#switch-shipment-type').hide();							
					$('.selectedItems').each(function() {
						$(this).removeClass("item_select2_single");
						$(this).closest('.clonedInput').find('.select2-container').remove();
					});
					$("#switch-shipment-type button").removeClass('active');
				}
				else if(data[0]['ordertype']==2){
					$(".shipbydatef").val(shipByServiceDate);
					//$(".clonedInput .price-group").show();
					$('.warehousing-panel').hide();
					$('.input_fn').show();
					$('#autocomplete-shipment-type').val("");
					$('#switch-shipment-type').hide();							
					$('.selectedItems').each(function() {
						$(this).removeClass("item_select2_single");
						$(this).closest('.clonedInput').find('.select2-container').remove();
					});
					$("#switch-shipment-type button").removeClass('active');
				}						
				else if(data[0]['ordertype']==3)
				{
					$(".shipbydatef").val(shipByIntegrationDate);
					$('.warehousing-panel').hide(); 
					$('.input_fn').hide();	
					$('#switch-shipment-type').show();	
					$('.selectedItems').each(function() {  
						$(this).addClass('item_select2_single');
						$(this).select2({width: '100%'});	
						$(this).closest('.clonedInput').find('.select2-container').show();								
					});							
				}
				else if(data[0]['ordertype']==4)
				{
					$(".shipbydatef").val(shipByWarehouseDate);
					$('.warehousing-panel').show();
					$('.input_fn').hide();
					$('#autocomplete-shipment-type').val("");
					$('#switch-shipment-type').hide();							
					$('.selectedItems').each(function() {
						$(this).addClass('item_select2_single');
						$(this).select2({width: '100%'});	
						$(this).closest('.clonedInput').find('.select2-container').show();								
					});
					$("#switch-shipment-type button").removeClass('active');
				}
			});								
			//
			$("#autocompleteitem_1").typeahead('destroy');
			$('.selectedItems').each(function() {
				loadIntegreationItems($("#customer_Id").val(), $(this));	
			});	
			$('.input_fn').each(function() {
				loadNotIntegrationItems($("#customer_Id").val(), $(this));
			});					
			//
			$(".input_fn").enable();
			//preload json 
			$.ajax({
				url: jsBaseUrl+"/ajaxrequest/getorderjsonloaded",
				dataType: 'json',
				success: function(data) {
					$.get(jsBaseUrl+data.name, function(data){_loadeditems=data;},'json');
				}
			});
		},
		ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#autocomplete-customer').val(),
		items : 10
	});
	//
	$('#autocompleteitem_1').on('change', function(){	
		if($(this).val() !== "")
		{
			$('#entry1').find('.package_option').attr('name', 'package_option[1][]');
			$('#entry1').find('.cleaning_option').attr('name', 'cleaning_option[1][]');
			$('#entry1').find('.testing_option').attr('name', 'testing_option[1][]');
			$('#entry1').find('.config_option').attr('name', 'config_option[1][]');
			$('#configuration-options').show();	
		}
		else 
			$('#configuration-options').hide();	
	});
});
//
$(document).on('change', '.select_ttl', function() {
	var e = $(this);
	var rowid = e[0].id.split('_')[1];
	//alert($('#autocompletevalitem_'+rowid).val());
	if($('#autocompletevalitem_'+rowid).val().length === 0){
		$('.qty-group').addClass('has-error'); // add the error class to show red input
	}
	else {
		//alert(e.val());
		$.ajax({
			url: jsBaseUrl+"/ajaxrequest/verifyqtystock",
			data: {
				model: $('#autocompletevalitem_'+rowid).val(),
				qty: e.val()
			},
			dataType: "json"
		}).done(function (data) {
			//alert(attr("class"));
			$('.help-block').remove(); // remove the error text
			if (data.success) {
				e.parents("div").eq(0).removeClass( "has-error" );
				$('#entry' + rowid + ' .qty-group').addClass('has-success'); // add the error class to show red input
			}else if(data.error){
				e.parents("div").eq(0).removeClass( "has-success" );
				$('#entry' + rowid + ' .qty-group').addClass('has-error'); // add the error class to show red input
				$('#entry' + rowid + ' .qty-group').append('<div class="help-block">Quantity exceed stock quantity!</div>'); // add the actual error message under our input					
			}
		});	
	}
});	
//
$('#addCustomerBtn').click(function(e) {
	//e.stopPropagation();
	$('#add-customer-form').show();	
});
//
$('#close-customer-order-form').click(function(e) {
	//e.stopPropagation();
	$('form#add-customer-form')[0].reset();
	$('#add-customer-form').hide();
});	
//
/***
 * script executed when form is submitted.
 */
 //
$(document).ready(function () {
	//submit order form
	$('#add-order-form').submit(function(event) {
		$('.price-group').removeClass('has-error');
		$('.desc-group').removeClass('has-error');
		$('.form-group').removeClass('has-error');
		$('#customer-group').removeClass('has-error');
		$('.col-sm-1').removeClass('has-error');
		$('.col-md-4').removeClass('has-error');
		$('.col-md-3').removeClass('has-error');
		$('.col-md-2').removeClass('has-error');
		$('.col-md-12').removeClass('has-error');
		$('.col-sm-6').removeClass('has-error');
		$('.col-md-14').removeClass('has-error');
		$('.help-block').remove(); // remove the error text
	//
		var vcustomer = $('input[name=customer]').val();
		var vlocation = $('#selectLocation').val();
		var vshipmenttype = '';
		var vpurchasetype = '';
		var vshipbydate = $('input[name=shipby]').val();
		var venduser = $('#enduserOrder').val();
		var vcustomerorder = $('#customerOrder').val();
		var vnotes = $('#orderNotes').val();
	//shipping fields 
		var vaccountnumber = $('input[name=accountnumber]').val();
		var vshippingcompany = $('#shippingcompany').val();
		var vdeliverymethod = $('#shippingmethod').val();	
	//
		var vqty = $('.select_ttl').val();
		var vdescription = $('.input_fn').val();
		var vprice = $('.priceorder').val();
		var error = 0;
	//
		if (vcustomer.length == 0) {
			$('#customer-group').addClass('has-error'); // add the error class to show red input
			$('#customer-group').append('<div class="help-block">Customer field is required!</div>'); // add the actual error message under our input
			error++;
		} 
		if (!vlocation) {
			$('#location-group').addClass('has-error'); // add the error class to show red input
			$('#location-group').append('<div class="help-block">Order location is required!</div>'); // add the actual error message under our input
			error++;
		} 
		if (vaccountnumber.length == 0) {
			$('#accountnumber-group').addClass('has-error'); // add the error class to show red input
			$('#accountnumber-group').append('<div class="help-block">Account Number field is required!</div>'); // add the actual error message under our input
			error++;
		} 
		if (!vshippingcompany) {
			$('#shippingcompany-group').addClass('has-error'); // add the error class to show red input
			$('#shippingcompany-group').append('<div class="help-block">Shipping Company field is required!</div>'); // add the actual error message under our input
			error++;
		} 
		if (!vdeliverymethod) {
			$('#shippingmethod-group').addClass('has-error'); // add the error class to show red input
			$('#shippingmethod-group').append('<div class="help-block">Delivery Method field is required!</div>'); // add the actual error message under our input
			error++;
		}	
	
		$('#switch-order-type .active').each(function(){
			vpurchasetype = $(this).data('switch-value'); 
		}); 
		
		$('#switch-shipment-type .active').each(function(){
			vshipmenttype = $(this).data('switch-value'); 
		});
		
		if (!vpurchasetype) {
			$('#puchasetype-group').addClass('has-error'); // add the error class to show red input
			$('#puchasetype-group').append('<div class="help-block">Type of Purchase is required!</div>'); // add the actual error message under our input
			error++;
		}
		vpurchasetype=parseInt(vpurchasetype);
		if(vpurchasetype == 3 && !vshipmenttype) {
			$('#shipment-group').addClass('has-error'); // add the error class to show red input
			$('#shipment-group').append('<div class="help-block">Shipment type is required!</div>'); // add the actual error message under our input
			error++;			
		}
		if (vshipbydate.length == 0) {
			$('#shipbydate-group').addClass('has-error'); // add the error class to show red input
			$('#shipbydate-group').append('<div class="help-block">Ship By is required!</div>'); // add the actual error message under our input
			error++;
		}
		if (vqty.length == 0) {
			$('.qty-group').addClass('has-error'); // add the error class to show red input
			error++;
		}
		if (vdescription.length == 0) {
			$('.desc-group').addClass('has-error'); // add the error class to show red input
			error++;
		}
		if (vprice.length == 0) {
			$('.price-group').addClass('has-error'); // add the error class to show red input
			$('.price-group').append('<div class="help-block">Price is required!</div>'); // add the actual error message under our input
			error++;
		}
		if(error) {
			event.preventDefault();
			//
			return false;
		}
	});	
	//some effects review in form
	$('.locationField').attr('tabindex', '-1');
	$('.input_sr').attr('tabindex', '-1');
	$('.comment_item_button').attr('tabindex', '-1');
	$("#customer-group").on('keydown', '#autocomplete-customer', function(e) { 
		$('#location-group #selectLocation').attr('tabindex', '-1');
		$('#location-group .select2-selection--single').attr('tabindex', '-1');
	});
	//tab key press on customer field 
	$("#customer-group").on('keydown', '#autocomplete-customer', function(e) { 
		var keyCode = e.keyCode || e.which; 

		if (keyCode == 9) { 
			//e.preventDefault(); 
			$("#selectLocation").focus(); 
			$('#selectLocation').select2("open");
		} 
	});	
	//
	$("#location-group").on('keydown', '#selectLocation', function(e) { 
		var keyCode = e.keyCode || e.which; 

		if (keyCode == 9) { 
			//e.preventDefault(); 
			$("#customerOrder").focus();
			//$('#selectLocation').val($('#selectLocation option:first-child').val()).trigger('change');
		} 
	});	
	$("#location-group").on('keydown', '.select2-search__field', function(e) { 
		$("#location-group").find("select option:eq(0)").prop("selected", true);
	});
	//					
	$("#selectLocation").next(".select2").find(".select2-selection").focus(function() {
		$("#selectLocation").select2("open");
	});
});
$(document).on('click touchstart', '.configuration_item_button', function(event) {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
	var model_id = $('#entry'+row+' .input_h').val();
//
	$.ajax({
		url: jsBaseUrl+"/orders/loadconfigurationform",
		data: {
			id: model_id,
			row: row
			//customerid: $("#customer_Id").val(),
			//"_csrf":jsCrsf
		},
		dataType: "json",
		encode          : true,
		complete:	function () {
			$('form#add-configuration-option-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser
				var $form = $(this);
				jQuery.validator.addClassRules('config_option', {
						required: true ,
						//minlength:3
					});				
                $form.validate({
                    rules: {                     
                        optionparentname: "required",
                    },
                    messages: {
                        optionparentname: "Title Of Configuration Option is required.",
                    },
                    submitHandler: function (form) {
						$.ajax({
							type: 'POST',  
							url: $form.attr('action'),
							data: $form.serialize(),
							dataType: "json",
							encode          : true								 
						}).done(function(data) {
							if(data.success) {
								$form[0].reset();
								$('#option-msg').html(data.html);
								$('#option-msg').show();							
								$("#option-msg").delay(2000).fadeOut("slow", function () { 
									$("#option-msg").hide(); 
									$('#manageConfigurations').modal('hide');
								});	
								$form.attr('action', jsBaseUrl+"/orders/manageconfigurations");
								//---- Load Configuration Options for purchase order type.
								if($("#purchasetype").val()==1){
									var url_1 = jsBaseUrl+"/modeloption/loadpurchaseorderoption?id=" + $form.find('#option_model_id').val() + "&entry_no=1";
									var entryrow = $form.find('#triggerRow').val();
									$.getJSON(url_1, function(data){		
										if(data.html !== "")
											$('#entry'+entryrow).find('.configuration-options').html('<h4><b>Configuration Options</b></h4>' + data.html);
									});	
								}else 
									$('#entry'+entryrow).find('.configuration-options').html('');								
							}
						});	
                    }
                });										
			});			
			//hide new option button
			$('#new-option-button').hide();
		}
	}).done(function (data) {
		if (data.success) {
			$("#configurations-options-manager").html(data.html);
			$('#manageConfigurations').modal('show'); 
		}
	});
});
//load edit content
$(document).on('change', '#selectCOption', function(event) {
	var $parentForm = $('form#add-configuration-option-form');
	var optionid = $(this).val();
	var modelid = $parentForm.find('#option_model_id').val();
	var entryrow = $parentForm.find('#triggerRow').val();
	//change action of form 
	$parentForm.attr('action', jsBaseUrl+"/orders/manageconfigurations?id="+optionid);
	$parentForm.find('#new-option-button').show();
	if(optionid.length > 0) {
		var url_1 = jsBaseUrl+"/orders/loadconfigurationform?id=" + modelid + "&row="+entryrow+"&optionid="+optionid;
		$.getJSON(url_1, function(data){		
			$parentForm.find("#configurations-options-manager").html(data.html);
		});					
	}
});
//
$(document).on('click', '#new-option-button', function () {
	var $parentForm = $('form#add-configuration-option-form');
	var modelid = $parentForm.find('#option_model_id').val();
	var entryrow = $parentForm.find('#triggerRow').val();
	//change action of form 
	$parentForm.attr('action', jsBaseUrl+"/orders/manageconfigurations");
	var url_1 = jsBaseUrl+"/orders/loadconfigurationform?id=" + modelid + "&row="+entryrow;
	$.getJSON(url_1, function(data){		
		$parentForm.find("#configurations-options-manager").html(data.html);
		$parentForm.find('#new-option-button').hide();
	});					
});
//first row edit button
$(document).on('click touchstart', '.edit_item_button', function(event) {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
	var model_id = "";
	if($("#purchasetype").val()!=3)
		model_id = $('#entry'+row+' .input_h').val();
	else 
		model_id = $('#entry'+row+' .selectedItems').val();
	//alert(model_id);
	//loadEditModel(model_id);
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
						
						newElem.find('.input_h').attr('id', 'editModelCustomerval' + newNum).attr('name', 'editModelCustomerval[]').val('');
						
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
//shipment autocomplete
//TODO : will be removed in version 2.0
/*$(function () {
	$('#autocomplete-shipment-type').autocomplete({
		serviceUrl: jsBaseUrl+"/ajaxrequest/listshipmenttype",
		onSelect: function (suggestion) {
			//alert('You selected: ' + suggestion.value + ', ' + suggestion.data);
		}
	});
});*/
	$(document).on('click', '#btnAddCOption', function () {
		var num     = $('.CclonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#centry' + num).clone().attr('id', 'centry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
		//
			newElem.find('.config_option').val('');
		//
			$('#centry' + num).after(newElem);	
			$('#btnDelCOption').attr('disabled', false);
	});
	$(document).on('click', '#btnDelCOption', function () {
	// confirmation
		var num = $('.CclonedInput').length;
		var current_num = num -1;
	// how many "duplicatable" input fields we currently have
		$('#centry' + num).slideUp('fast', function () {$(this).remove(); 
	// if only one element remains, disable the "remove" button
		if ( current_num === 1)
			$('#btnDelCOption').attr('disabled', true);
	// enable the "add" button
		$('#btnAddCOption').attr('disabled', false).prop('value', "add section");});
	// enable the "add" button
		$('#btnAddCOption').attr('disabled', false);
		return false;
	});
 
	$('#btnDelCOption').attr('disabled', true);	
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
	$('#btnAdd').click(function () {
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
			newElem.find('.configuration_item_button').attr('id', 'Config_' + newNum + '');			
			newElem.find('.comment').attr('id', 'itemNote_' + newNum + '');	
			
			newElem.find('#Edit_' + newNum).hide();
			newElem.find('#Comment_' + newNum).hide();
			//newElem.find('#configOptions_' + newNum).hide();
			newElem.find('.configuration_item_button').hide();
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
		//newElem.find('.input_fn').focus();
 
	// enable the "remove" button
		$('#btnDel').attr('disabled', false);
		newElem.find('.package_option').attr('id', 'package_option' + newNum + '').attr('name', 'package_option[' + newNum + '][]');
		newElem.find('.cleaning_option').attr('id', 'cleaning_option' + newNum + '').attr('name', 'cleaning_option[' + newNum + '][]');
		newElem.find('.testing_option').attr('id', 'testing_option' + newNum + '').attr('name', 'testing_option[' + newNum + '][]');
		newElem.find('.config_option').attr('id', 'config_option' + newNum + '').attr('name', 'config_option[' + newNum + '][]');
		$('#Edit_' + newNum).on('click', function(event) {
			var e = $(this);
			var row = e[0].id.split('_')[1];	
			//alert(row);
			//$('#Edit_' + row).hide();
			//$('#entry'+row+' .input_fn').val('');
			//$('#entry'+row+' .input_fn').removeAttr('readonly'); 
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
		var _type = $("#purchasetype").val();
		if(_type==1){
			newElem.find('.selectedItems').removeClass("item_select2_single");
			//$(".clonedInput .price-group").show();   
			$('.warehousing-panel').hide();
			$('.input_fn').show();
		}
		else if(_type==2){
			newElem.find('.selectedItems').removeClass("item_select2_single");
			//$(".clonedInput .price-group").show();
			$('.warehousing-panel').hide();
			$('.input_fn').show();
		}						
		else if(_type==3)
		{
			//$(".clonedInput .price-group").hide();
			$('.warehousing-panel').hide(); 
			
			$('.input_fn').hide();	
			
			$(this).addClass('item_select2_single');
						
			newElem.find('.selectedItems').select2({width: '100%'});
			
			newElem.find('.select2-container').show();		
		}
		else if(_type==4)
		{
			//$(".clonedInput .price-group").show();
			$(this).addClass('item_select2_single');
			
			$('.warehousing-panel').show();
			
			$('.input_fn').hide();
						
			newElem.find('.selectedItems').select2({width: '100%'});
			
			newElem.find('.select2-container').show();
		}
		//
		var customer = $("#autocomplete-customer").val();
		var url_models = jsBaseUrl+"/ajaxrequest/loadmodels?customer="+customer;
		//
		$('#autocompleteitem_' + newNum).typeahead({
			name: 'Models',
			onSelect: function(item) {
				if($("#purchasetype").val()==1){
					var url_1 = jsBaseUrl+"/modeloption/loadpurchaseorderoption?id=" + item.value + "&entry_no=" + newNum + "&customerid=" + $("#customer_Id").val();
					$.getJSON(url_1, function(data){
						//alert(data.toSource());
						if(data.html !== "")
							$('#entry' + newNum + ' .configuration-options').html('<h4><b>Configuration Options</b></h4>' + data.html + '');
					});	
				}else 
					$('#entry' + newNum + ' .configuration-options').html('');	
				$('#Comment_' + newNum ).show();
				$('#Config_' + newNum ).show();
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
						$('#item-available-in-stock-' + newNum ).html(data[0].stock + ' Available in AMS inventory');
					});	
				}
			},				
			source:_loadeditems,
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
 
	// right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
		//if (newNum == 10)
		//$('#btnAdd').attr('disabled', true).prop('value', "You've reached the limit");
	});
 
	$('#btnDel').click(function () {
	// confirmation
		var num = $('.clonedInput').length;
		var current_num = num -1;
	// how many "duplicatable" input fields we currently have
		$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
	// if only one element remains, disable the "remove" button
		if ( current_num === 1)
			$('#btnDel').attr('disabled', true);
	// enable the "add" button
		$('#btnAdd').attr('disabled', false).prop('value', "add section");});
	// remove the last element
	/*var item_txt = "";
	if(current_num==1)
		item_txt = "item";
	else 
		item_txt = "items";
	$('#item-count-order').html(current_num+' ' + item_txt);*/
	// enable the "add" button
	$('#item-count-order').html('Total cost : $' + checkOrderAmount(true));
		$('#btnAdd').attr('disabled', false);
		return false;
	});
 
	$('#btnDel').attr('disabled', true);
});