//---- F
$.prototype.enable = function () {
	$.each(this, function (index, el) {
		$(el).removeAttr('disabled');
	});
}
//
$.prototype.disable = function () {
	$.each(this, function (index, el) {
		$(el).attr('disabled', 'disabled');
	});
}
//	
function redirectOrders()
{
	window.location =  jsBaseUrl+"/orders/index";
}
function redirectCustomers()
{
	window.location =  jsBaseUrl+"/customers/index";
}
function redirectPurchase()
{
	window.location =  jsBaseUrl+"/purchasing/index";
}
function redirectUser()
{
	window.location =  jsBaseUrl+"/users/index";
}
//
function ViewCurrentPdf()
{
	$.get(jsBaseUrl+"/ajaxrequest/getfileuploadname", function(data){
		var uri = data.uri;
		$("#pdfViewer").modal('toggle');
		if(uri.toLowerCase().indexOf(".pdf") >= 0)
		{
			var myPDF = new PDFObject({ 					
				url: uri
			  }).embed('document-viewer'); 	
		}			
		else{
			$("#document-viewer").html('<div style="text-align:center;font-size:16px;"><b>Document can\'t be loaded. Seems file missing!</b></div>');
		}
	},'json');		
}
//
function OpenPdfViewer(url)
{
	$("#pdfViewer").modal('toggle');
	if(url.toLowerCase().indexOf(".pdf") >= 0)
	{
		var myPDF = new PDFObject({ 
			
			url: url

		  }).embed('document-viewer'); 	
	}			
	else{
		$("#document-viewer").html('<div style="text-align:center;font-size:16px;"><b>Document can\'t be loaded. Seems file missing!</b></div>');
	}
}
//
function showPicture(url)
{
	$('#showImage').modal('toggle');
	$('#imageDisplay').attr('src', url);
	$('#imageDisplay').attr('width', 240);
}

function EditLocationDetails(locationid)
{
   $.ajax({
		url: jsBaseUrl+"/location/editdetails",
		data: {
			id: locationid
		},
		dataType: "json",
		complete: function() {
			$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
			$('#connection_type').change(function (e) {
				//alert($(this).val());
				if($(this).val()=='Dial-up')
					$('#requireninedialout-group').show();
				//
				//$("[name='requireninedialout']").bootstrapSwitch("size", "mini");
			});
		}
	}).done(function (data) {
		//alert(data.locationName);
		if (data.success) {	
			$('#edit-configuration-settings').html(data.locationName);
			$('#location-details-content-form').html(data.html);
			$('#LocationDetailsModal').modal('show');
		}		
	});
}

function UpdateLocationSettings(id)
{
   $.ajax({
		url: jsBaseUrl+"/location/loadlocationsettings",
		data: {
			id: id
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#loaded-location-setting-content").html(data.html);
		}
	});	
}

//
function ModelsViewer(id)
{
   $.ajax({
		url: jsBaseUrl+"/site/loadmodels",
		data: {
			id: id
		},
		dataType: "json",
		success: function(data){
			if (data.success) {
				$("#loaded-slider-content").html(data.html);
				
				$('#showSliderImage').modal('show');
										
                        $('#myCarousel').carousel({interval: 4000});
                        // handles the carousel thumbnails
                        $('[id^=carousel-selector-]').click( function(){
                          var id_selector = $(this).attr("id");
                          var id = id_selector.substr(id_selector.length -1);
                          id = parseInt(id);
                          $('#myCarousel').carousel(id);
                          $('[id^=carousel-selector-]').removeClass('selected');
                          $(this).addClass('selected');
                        });

                        // when the carousel slides, auto update
                        $('#myCarousel').on('slid', function (e) {
                          var id = $('.item.active').data('slide-number');
                          id = parseInt(id);
                          $('[id^=carousel-selector-]').removeClass('selected');
                          $('[id=carousel-selector-'+id+']').addClass('selected');
                        });
                        $('#thumbCarousel').carousel();
//			 $(".bxslider img").load(function(){
//				$(window).trigger('resize');
//			 },200);
//				setTimeout(function(){
//					$('.bxslider').bxSlider({});
//				},200);
			}
		},
		complete: function (result) {
			//$(window).trigger('resize');
		},
		//beforeSend: function() {$.getScript(jsBaseUrl+"/public/js/bxslider/jquery.bxslider.min.js", function( data, textStatus, jqxhr ) {});},
	}).done(function (data) {
		//alert(data.toSource());
		/*if (data.success) {
			$("#loaded-slider-content").html(data.html);
		}
		var slider = $('.bxslider').bxSlider({
                 // auto:true,
				  pagerCustom: '#bx-pager'
                });
		slider.destroySlider();
		$('#showSliderImage').modal('show');
		setTimeout(function(){
			slider.reloadSlider();
		},200);		*/
		$(window).trigger('resize');
                
		
		//slider.destroySlider();
		
//		setTimeout(function(){
//			slider.reloadSlider();
//		},200);	
	});	
}
//
function openSerialWindow(id, item)
{
	//alert(id);
   $.ajax({
		url: jsBaseUrl+"/orders/default/serialform",
		data: {
			id: id,
			item: item
		},
		dataType: "json"
	}).done(function (data) {
		//alert(data.toSource());
		if (data.success) {
			$("#serialsInput").html(data.html);
			$(".countserializeditems").html(data.itemserialized);
			$(".countnotserializeditems").html(data.itemserializedornot);
		}
		$('#addSerials').modal('show');
	});
}
function openOSerialWindow(customer, item, qty, row)
{
   $.ajax({
		url: jsBaseUrl+"/receiving/default/oserialform",
		data: {
			customer: customer,
			model: item,
			quantity: qty,
			currentquantity: 0,
			row : row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#serialsInput").html(data.html);
			$(".countserializeditems").html(data.itemserialized);
			$(".countnotserializeditems").html(data.itemserializedornot);			
		}
		$('#addSerials').modal('show');
	});
}
//
function openOrderLocation(id)
{
	if(id.length!=0) {
		$('#locerror').remove();
		$('#l_customer').val(id); 
		$('#addLocation').modal('toggle');
	}
	else {
		$('#locerror').remove();
		$('#row-master').append('<div class="alert alert-warning" id="locerror"><strong>Failed!</strong> You must select customer before!</div>');
	}
}	
//
function loadCustomerDetails(id) {
	$.ajax({
		url: jsBaseUrl+"/customers/default/view",
		data: {
			id: id
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#detaisOfCustomer").html(data.html);
			$("#customerDetails").modal('show');
		}
	});
}

function loadScheduleDelivery(itemid) 
{
	$.ajax({
		url: jsBaseUrl+"/purchasing/default/scheduledelivery",
		beforeSend: function() {
			$('#loading').show();
		},
		data: {
			item: itemid
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#sch_modelname").html(data.modelname);
			$("#schedule-delivery-content").html(data.html);
			$("#ScheduleDelivery").modal('show');
			$('#loading').hide();
		}
	});	
}

//--- H
function checkOrderAmount(rev)
{
	var qtys = new Array();
	var prices = new Array();
	$('.select_ttl').each(function(){
		if($(this).val().length!=0)
			qtys.push($(this).val());
		else 
			qtys.push(0);
	});
	$('.priceorder').each(function(){
		if($(this).val().length!=0)
			prices.push($(this).val());
		else 
			prices.push(0);
	});
	QLen = qtys.length;
	PLen = prices.length;
	//alert(QLen);
	//alert(PLen);
	var sum = 0;
	if(rev===true)
		QLen = QLen-1;
	for (i = 0; i < QLen; i++) {
		sum += qtys[i] * prices[i];
	} 	
	//
	return sum.toFixed(2);
}
//
function validateRequest(id, description, manpartnum)
{
	//alert(description);
	$('#request-vitem-form')[0].reset();
	$('.col-sm-6').removeClass('has-error');
	$('.col-md-14').removeClass('has-error');
	$('.help-block').remove(); // remove the error text
	$('#requestItemValidateModalLabel').html (' <span class="glyphicon glyphicon-list-alt"></span> <span style="color:#08c;">Validation of Item "<b>' + description + '</b>"</span>');
	$('textarea[name=rv_description]').text(description);
	$('input[name=rv_manpart]').val(manpartnum);
	$('input[name=_requestid]').val(id);
	$('#requestItemValidate').modal('toggle');
}
//
function loadSerializedNextModel(itemid, orderid)
{
	/*$("#serialsInput").slideUp({duration: 100, queue: false})
		.fadeOut({duration: 100, queue: false})
		.promise().done(function() {
			$("#serialsInput").html('');
		});	*/
	//
	//setTimeout(function(){
	   $.ajax({
			url: jsBaseUrl+"/orders/default/serialform",
			data: {
				id: orderid,
				item: itemid,
				next: true
			},
			dataType: "json"
		}).done(function (data) {
			//alert(data.toSource());
			if (data.success) {					        		
				//$("#serialsInput").slideDown({duration: 100, queue: false}).fadeIn({duration: 100, queue: false}).promise().done(function() {
					$("#serialsInput").html(data.html);
					$(".countserializeditems").html(data.itemserialized);
					$(".countnotserializeditems").html(data.itemserializedornot);
				//});							                   
			}
			$('#qserialnumber').focus();
			$('#addSerials').modal('show');
		});
	//},500);			   			
}
function loadReceivingSerializedNextModel(model, customer, quantity, currentquantity, row)
{
	   $.ajax({
			url: jsBaseUrl+"/receiving/default/oserialform",
			data: {
				model: model,
				customer: customer,
				quantity: quantity,
				currentquantity: currentquantity,
				row: row,
				next: true
			},
			dataType: "json"
		}).done(function (data) {
			//alert(data.toSource());
			if (data.success) {					        		
				$("#serialsInput").html(data.html);
				$(".countserializeditems").html(data.itemserialized);
				$(".countnotserializeditems").html(data.itemserializedornot);
			}
			$('#qserialnumber').focus();
			$('#addSerials').modal('show');
		});
}

function addSerialWithOrNotValidation(vserialnumber, vcurrentmodel, vquantity, vcustomer, vlocation, jsCrsf, skipValidation=false, vcurrent_quantity, triggerRow)
{
 	var formData = {
		"serial": vserialnumber, 
		"currentmodel": vcurrentmodel,
		"quantity": vquantity,
		"customerId": vcustomer,
		"location": vlocation,
		"_csrf":jsCrsf
	};
	//
	if(skipValidation)
	{
		formData.skipValidation='true';
	}
	//
   $.ajax({ 
		type        : 'POST',
		url: jsBaseUrl+"/receiving/default/saveserial",
		data: formData,
		dataType: "json",
		encode: true		
	}).done(function (data) {
		//alert(vserialnumber);
		if(data.success){
			ion.sound.play("success");
			$('#add-receiving-serial-form')[0].reset(); 
			if(data.done)
			{
				$('#addSerials').modal('hide'); 
				$('#entry'+triggerRow+' .rquantity').attr('disabled', true);
				$('#entry'+triggerRow+' .input_fn').attr('disabled', true);
				$('#entry'+triggerRow+' .add_serial_button').attr('disabled', true);
				$('#entry'+triggerRow+' .clear_serialized-group').show();
			}
			$(".countserializeditems").html(data.current_quantity);
		}
	});
}

function openNonSerializedModal(item)
{
   $.ajax({
		url: jsBaseUrl+"/orders/default/pickingconfirmform",
		data: {
			item: item
		},
		complete: function() {

		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					        		
			$("#load-picking-confirmed-content").html(data.html);
			//
			$('form#picking-non-serialized-confirm-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser
				event.stopPropagation();
				var form = $(this);
				//
				//if($('#serialized').prop('checked') || $('#serialized').prop('checked') || $('#serialized').prop('checked')) //update model options
				//{
					$.ajax({
						type: 'POST',  
						url: jsBaseUrl+"/inventory/setmodeloptions",
						data: form.serialize(),
						dataType: "json",
						encode          : true								 
					}).done(function(data) {
					});						
				//}	
				//
				if($('#serialized').prop('checked'))
					openSerialWindow(data.order, data.model);
				else 
				{			
					$.ajax({
						type: 'POST',  
						url: form.attr('action'),
						data: form.serialize(),
						dataType: "json",
						encode          : true								 
					}).done(function(data) {
						//form[0].reset();
						if(data.success)
						{
							$('#pick-deliver-button').show();
							$('#picked-count-button-'+ data.id).html(parseInt($('#picked-count-button-'+ data.id).text())+parseInt($('#confirmed-qty').text()));
							$('#instock-count-button-'+ data.id).html(parseInt($('#instock-count-button-'+ data.id).text())-parseInt($('#confirmed-qty').text()));							
							new PNotify({
								title: 'Notifications',
								text: data.itemname + ' Items has been successfully picked!',
								type: 'success',
								styling: "bootstrap3",
								opacity: 0.8,
								delay: 5000
							});	
						}
						else 
						{
							new PNotify({
								title: 'Notifications',
								text: data.message,
								type: 'error',
								styling: "bootstrap3",
								opacity: 0.8,
								delay: 5000
							});								
						}
						$('#PickingConfirmation').modal('hide');
					});	
				}
			});
			$('#PickingConfirmation').modal('show');
		}		
	});	
}
/***
 * Order create core functions
 */
function loadNotIntegrationItems(customerid, e)
{
	$.get(jsBaseUrl+"/ajaxrequest/loadmodels?customer=" + customerid, function(data){
		//alert(data);
		e.typeahead({ 
			onSelect: function(item) {
				e.closest('.clonedInput').find('.comment_item_button').show();  
				e.closest('.clonedInput').find('.configuration_item_button').show();  
				e.closest('.clonedInput').find('.item_config_options_button').show();
				e.closest('.clonedInput').find('.edit_item_button').show();
				//---- Load Configuration Options for purchase order type.
				if($("#purchasetype").val()==1){
					var url_1 = jsBaseUrl+"/modeloption/loadpurchaseorderoption?id=" + item.value + "&entry_no=1&customerid="+ customerid;
					$.getJSON(url_1, function(data){		
						//alert(data.toSource());
						if(data.html !== "")
							e.closest('.clonedInput').find('.configuration-options').html('<h4><b>Configuration Options</b></h4>' + data.html);
					});	
				}else 
					e.closest('.clonedInput').find('.configuration-options').html('');
				//--- End loading.
				//---- get model selected id
				e.closest('.clonedInput').find('.input_h').val(item.value);
				//----
				//---- Remember pricing.
				var url_2 = jsBaseUrl+"/ajaxrequest/getpricing?customer=" + $("#customer_Id").val() + "&ordertype=" + $("#purchasetype").val() + "&idmodel=" + item.value;
				$.getJSON(url_2, function(data){
					if(data[0].price.length!==0)
						e.closest('.clonedInput').find('.priceorder').val(data[0].price);
					else 
						e.closest('.clonedInput').find('.priceorder').val('0.00');
				});		
				//----
				//---- show item in stock
				if($("#purchasetype").val()==1){//asset stock
					var url_3 = jsBaseUrl+"/ajaxrequest/checkstockavailable?model=" + item.value + "&customer=4";
					$.getJSON(url_3, function(data){
						e.closest('.clonedInput').find('.itemqtystock').html(data[0].stock + ' Available in AMS inventory');
					});	
				}
				//----
			},				
			source:data,
			autoSelect: true,
			items : 10	
		});
	},'json');						
}
//
function loadIntegreationItems(customerid, e)
{
	var items="";
	//var customerid = ;
	var url = jsBaseUrl+"/ajaxrequest/listorderitems?customer=" + customerid;
	//var e = ;
	//alert(e[0].id);
	var xhr = $.get(url, function(data){
		items+='<option selected="selected" value="">Select An Item</option>';
		$.each(data,function(index,item){
			items+="<option value='"+item.id+"'>"+item.name+"</option>";
		});
		e.html(items); 
	}, 'json');	
	xhr.done(function(data) {
		return true;
	});
	//return false;
	//
}
//
function Loadlocations(customerid, locationField, selectedIndex)
{
	var items="";
	var url = jsBaseUrl+"/ajaxrequest/listorderlocations?customer=" + customerid; 
	selectedIndex = parseInt(selectedIndex);
	$.getJSON(url, function(data){  
		items+='<option value="">Select A Location</option>';
		$.each(data,function(index,item){ 
			items+="<option value='"+item.id+"'>"+item.name+"</option>";					
		});
		locationField.html(items); 		
		//
		if(selectedIndex != 0)
		{	
			locationField.select2("val", selectedIndex);
		}						
	}); 				
}

function Loadcustomers(customerField)
{
	var items="";
	var url = jsBaseUrl+"/ajaxrequest/listcustomers"; 
	$.getJSON(url, function(data){  
		$.each(data,function(index,item){ 
			//alert(item.name);
			items+="<option value='"+item.id+"'>"+item.name+"</option>";					
		});
		customerField.html(items); 							
	}); 				
}
//
//function Loadcustomers(customerField, selectedIndex)//TODO : send array in function
//
function Loadclocations(customerid, locationField, selectedIndex, customText) //TODO: remove to previous first
{	
	var items="";
	var url = jsBaseUrl+"/ajaxrequest/listorderlocations?customer=" + customerid; 
	selectedIndex = parseInt(selectedIndex);
	$.getJSON(url, function(data){  
		if(customText.length === 0)
			items+='<option value="">Select A Location</option>';
		else 
			items+='<option value="">'+ customText +'</option>';
		$.each(data,function(index,item){ 
			items+="<option value='"+item.id+"'>"+item.name+"</option>";					
		});
		locationField.html(items); 		
		//
		if(selectedIndex != 0)
		{	
			locationField.select2("val", selectedIndex);
		}						
	}); 				
}
//
function openMailer(t, type)
{
	var _url;
	if(type==1)
		_url = jsBaseUrl+"/orders/default/sendmailform";
	else if(type==2)
		_url = jsBaseUrl+"/purchasing/default/sendmailform";
	else if(type==3)
		_url = jsBaseUrl+"/orders/default/qsendmailform";
	else if(type==4)
		_url = jsBaseUrl+"/billing/sendmailform";
	//alert(_url);
   $.ajax({
		url: _url,
		beforeSend: function(){
			$('#loading').show();  
		},
		data: {
			id: t
		},
		dataType: "json"
	}).done(function (data) {
		$('#loading').hide();
		if (data.success) {					    
			if(type==1)
				$('#send-mail-form').attr('action', jsBaseUrl+"/orders/default/sendmail");
			else if(type==2)
				$('#send-mail-form').attr('action', jsBaseUrl+"/purchasing/default/sendmail");
			$('#loaded-content').html(data.html);
			$('#sendEmail').modal('show');
		}
	});
}

function loadCustomerLocation(id, customer)
{
	var _url = jsBaseUrl+"/site/loadlocationform?customer="+customer;
	//var _url = jsBaseUrl+"/site/loadlocationform";
	var _title = "Add A New Store";
	if(id!="")
	{
		_url = _url+"&id="+id; 
		_title = "Edit A Store";
	}
	//
	//alert(_url);
   $.ajax({
		url: _url,
		beforeSend: function(){$('#loading').show();},
		complete: function() {
			$('#loading').hide();
			$('.location_zip').focusout(function(e) {
				var id = $(this)[0].id;
				id = id.replace('_zip', '');
				//alert(id);
				var client = new XMLHttpRequest();
				//var data, response;
				client.open("GET", "http://api.zippopotam.us/us/"+$(this).val(), true);
				client.onreadystatechange = function() {
					if(client.readyState == 4) {
						//alert($.paseXML(client.responseText));
						var response = eval ("(" + client.responseText + ")");
						//response.places[0].state
						//alert(response.places[0]['place name']);
						$('#' + id + '_state').val(response.places[0]['state abbreviation']);//state
						$('#' + id + '_city').val(response.places[0]['place name']);//city
						$('#' + id + '_country').val(response.country);//country
					};
				};

				client.send();
			});				
		},
		dataType: "json"
	}).done(function (data) {  
		if (data.success) {					    
			$('#rep_location_title').html(_title);
			$('#loaded-location-content').html(data.html);
			$('#saveLocation').modal('show');
		}
	});		
}

/*function EditLocation(locationid)
{
   $.ajax({
		url: jsBaseUrl+"/orders/default/relatedservice",
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
			$('#service_number').html(data.order_number);
			$('#loaded-service-info-content').html(data.html);
			$('#relatedServiceDetails').modal('show');
		}
	});	
}*/
//
function loadUserInfo(id)
{
	var url = jsBaseUrl+"/ajaxrequest/getuserdetails?id=" + id;
	//alert(url);
	$.getJSON(url, function(data){
		$('#username').val(data.username);
	});
}
//
function openRefurbModal(order, model)
{
   $.ajax({
		url: jsBaseUrl+"/orders/default/refurbishform",
		data: {
			order: order,
			model: model 
		},
		dataType: "json",
		complete: function() {
			$("[name='preowneditems']").bootstrapSwitch();
			$("[name='requiretestingreferb']").bootstrapSwitch();
		}
	}).done(function (data) {
		if (data.success) {					    
			$('#refurbishModal .container').html(data.html);
			$('#refurbishModal').modal('show');
		}
	});	
}
//
function openOptionsModal(order, model)
{
   $.ajax({
		url: jsBaseUrl+"/orders/default/purchaseoptionsform",
		data: {
			order: order,
			model: model
		},
		dataType: "json",
		complete: function() {
			//
			$(document).on("click touchstart", "#switch-cleaning-options [data-switch-set]", function() {
				$("#switch-cleaning-options button").removeClass('active');
				$(this).addClass('active');
				$("#switch-cleaning-options button").css({"background-color": "", "color": "", "box-shadow": ""});
				$(this).css({"background-color": "#26B99A", "color": "#FFF", "box-shadow": "inset 0px 0px 5px #BBB"});
				$("[name='cleaning_option']").val($(this).data("switch-value"));
			});
			//
			$(document).on("click touchstart", "#switch-testing-options [data-switch-set]", function() {
				$("#switch-testing-options button").removeClass('active');
				$(this).addClass('active');
				$("#switch-testing-options button").css({"background-color": "", "color": "", "box-shadow": ""});
				$(this).css({"background-color": "#26B99A", "color": "#FFF", "box-shadow": "inset 0px 0px 5px #BBB"});
				$("[name='testing_option']").val($(this).data("switch-value"));
			});		
			//
			$('form#add-purchase-option-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser		
				$('#add-purchase-option-form #testing-options-group').removeClass('has-error');
				$('#add-purchase-option-form .help-block').remove(); // remove the error text				
				var $form = $(this);
				var error = 0;
				if ($("[name='cleaning_option']").val().length == 0 && $("[name='testing_option']").val().length == 0) {
					$('#testing-options-group').addClass('has-error'); // add the error class to show red input
					$('#testing-options-group').append('<div class="help-block" style="text-align:center;"><b>One of these Options should be choosen</b></div>'); // add the actual error message under our input
					error++;
				} 
				if(!error) {		
					$.ajax({
						type: 'POST',  
						url: $form.attr('action'),
						data: $form.serialize(),
						dataType: "json",
						encode          : true								 
					}).done(function(data) {
						if(data.success) {
							$form[0].reset();
							$('#purchase-option-msg').html(data.html);
							$('#purchase-option-msg').show();							
							$("#purchase-option-msg").delay(2000).fadeOut("slow", function () { 
								$("#purchase-option-msg").hide(); 
								$('#addOptions').modal('hide');
								$('#options-loaded-content').html('');
							});								
						}
					});	
				}
			});
		}
	}).done(function (data) {
		if (data.success) {					    
			$('#item-name').html(data.itemname);
			$('#options-loaded-content').html(data.html);
			$('#addOptions').modal('show');
		}
	});		
}
//
function openConfirmationModal(url)
{
	$('#PickingConfirmation').modal('show');
	$(document).on("click touchstart", "#confirmButton", function() {
		window.location =  url;
	});
}	
/**
 * Load locations others details
 */
function loadlocationotherdetails(ishippingcompany, iaccountnumber, ideliverymethod, ilocation)
{
	var url = jsBaseUrl+"/ajaxrequest/getshippingotherdetailsfromlocation?locationid="+ilocation.val();
	//alert(url);
	$.getJSON(url, function(data){
		iaccountnumber.val(data.s1);
		ishippingcompany.val(data.s2);
		loadShippingMethods(ishippingcompany, ideliverymethod, data.s3);
		$("#switch-shipping-detail-tab button").removeClass('active');
		$('#switch-shipping-detail-tab button').each(function(){
			if(parseInt($(this).data('switch-value'))==2)
				$(this).addClass('active');
		});				
	});		
}

/***
 ** confirmation dialog box
 */
 function callDialog(msg, url)
 {
		
 }
 /**
  */
 function confirmPicklistTurning(order)
 {
   $.ajax({
		url: jsBaseUrl+"/orders/default/picklistreadyform",
		data: {
			id: order
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#detaisOfPicklistReady').html(data.html);
			if($('#detaisOfPicklistReady').find('#delivery-confirmation-items').length)
				$('#main-delivery-items').show();
			else 
				$('#main-delivery-items').hide();
			//alert(parseInt(data.verificationcount));
			if(parseInt(data.verificationcount) > 7)
				window.location = jsBaseUrl+"/orders/deliveritems?id="+getUrlParameter('id');
			else 
				$('#picklistReady').modal('show');
		}
	});	
 }
 
function confirmReadyButton(type, item, order)
{
	var _order_type_items_class = "";
   $.ajax({
		url: jsBaseUrl+"/orders/default/picklistreadymodel",
		data: {
			type: type,
			itemid: item,
			orderid: order,
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {	
			if(parseInt(order) == 0)
			{
				$('#btn_'+type+'-'+item).removeClass('btn-warning');
				$('#btn_'+type+'-'+item).addClass('glyphicon glyphicon-ok btn btn-success');
				$('#btn_'+type+'-'+item).removeAttr('onClick');
				$('#btn_'+type+'-'+item).html("");
				$('#pick-deliver-button').hide();
			}
			else 
			{
				if(type==0)
				{
					$('.shipping-m-item, .cleaning-m-item, .testing-m-item').removeClass('btn-warning');
					$('.shipping-m-item, .cleaning-m-item, .testing-m-item').addClass('glyphicon glyphicon-ok btn btn-success');
					$('.shipping-m-item, .cleaning-m-item, .testing-m-item').removeAttr('onClick');
					$('.shipping-m-item, .cleaning-m-item, .testing-m-item').html("");		
					$('#main-delivery-items').hide();
				}
				else 
				{
					if(type==1)
						_order_type_items_class = "shipping-m-item";
					else if(type==2)
						_order_type_items_class = "cleaning-m-item";
					else if(type==3)
						_order_type_items_class = "testing-m-item";		
					//
					$('.'+_order_type_items_class).removeClass('btn-warning');
					$('.'+_order_type_items_class).addClass('glyphicon glyphicon-ok btn btn-success');
					$('.'+_order_type_items_class).removeAttr('onClick');
					$('.'+_order_type_items_class).html("");	
				}
			}
			//
			$('.shipping-m-item').each(function() {
				var e = $(this);
				var row = e[0].id.split('-')[1];
				if(e.hasClass('btn-success'))
				{
					$('#pickbutton'+ row).removeClass('btn-warning')
					$('#pickbutton'+ row).addClass('btn-success');
				}
			}); 
			//
			$('.cleaning-m-item').each(function() {
				var e = $(this);
				var row = e[0].id.split('-')[1];
				if(e.hasClass('btn-success'))
				{
					$('#pickbutton'+ row).removeClass('btn-warning')
					$('#pickbutton'+ row).addClass('btn-success');
				}
			}); 
			//
			$('.testing-m-item').each(function() {
				var e = $(this);
				var row = e[0].id.split('-')[1];
				if(e.hasClass('btn-success'))
				{
					$('#pickbutton'+ row).removeClass('btn-warning')
					$('#pickbutton'+ row).addClass('btn-success');
				}
			}); 
		}
	});
}

function isUrl(s) {
	if(/(^|\s)((https?:\/\/)?[\w-]+(\.[\w-]+)+\.?(:\d+)?(\/\S*)?)/gi.test(s)) {
		return true;
	} else {
		return false;
	}
}

var getUrlParameter = function getUrlParameter(sParam) {
    var sPageURL = decodeURIComponent(window.location.search.substring(1)),
        sURLVariables = sPageURL.split('&'),
        sParameterName,
        i;

    for (i = 0; i < sURLVariables.length; i++) {
        sParameterName = sURLVariables[i].split('=');

        if (sParameterName[0] === sParam) {
            return sParameterName[1] === undefined ? true : sParameterName[1];
        }
    }
};