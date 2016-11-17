var items="";
var url = jsBaseUrl+"/ajaxrequest/listrepresentativelocations?customer=178";
$.getJSON(url, function(data){  
	$.each(data,function(index,item){ 
		items+="<option value='"+item.id+"'>"+item.name+"</option>";					
	});
	$('#selectlocation').html(items); 								
});

$(document).on("click touchstart", "[id^=return-label-service]", function(){  
	var e = $(this);
	var row = e[0].id.split('_')[1];
	OpenReturnLabelModal(row);
});
					
//
$('form#add-rmaorder-form').submit(function(event) {
	if(!$('[id^="checkbox_"]').is(':checked'))
	{
		event.preventDefault(); // Prevent the form from submitting via the browser
		new PNotify({
			title: 'Notifications',
			text: 'Please choose an item before creating your RMA request.',
			type: 'error',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});			
	}
});
//
$('form#rma-add-location-form').submit(function(event) {
	event.preventDefault(); // Prevent the form from submitting via the browser	
	//
	var form = $(this);
	var id = $('#l_location').val();
	var customer = $('#l_customer').val();
	var storenum = $('#storenum').val();
	var storename = $('#storename').val();
	var address = $('#location_address').val();
	var address2 = $('#location_secondaddress').val();
	var country = $('#location_country').val();
	var city = $('#location_city').val();
	var state = $('#location_state').val();
	var zipcode = $('#location_zip').val();
	var email = $('#location_email').val();
	var phone = $('#location_phone').val();
	var error = 0;
	var _message = "";
	//alert($('[name="_csrf"]').val());
	//
	if(storenum.length==0 && storename.length==0)
	{
		error++;
		_message = "Store Number Or Store Name is required!";
	} else if(address.length==0) {
		error++;
		_message = "Address field required !";		
	} else if(city.length==0) {
		error++;
		_message = "City field required !";			
	} else if(zipcode.length==0) {
		error++;
		_message = "Zip field required !";			
	} else if(!state) {
		error++;
		_message = "State field required !";			
	}
	//
	if (error) {
		new PNotify({
			title: 'Notifications',
			text: _message,
			type: 'error',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});	
	} else {
		// process the form
		$.ajax({
			type        : 'POST', 
			url         : jsBaseUrl+"/ajaxrequest/addlocation",
			data        : form.serialize(),
			dataType    : 'json',
			encode      : true,
			error: function (xhr, ajaxOptions, thrownError) {
				/*console.log(xhr.status);
				console.log(xhr.responseText);
				console.log(thrownError);*/
			}
		}).done(function(data) {
			if(data.success)
			{
				var url = jsBaseUrl+"/ajaxrequest/listrepresentativelocations?customer=178";
				$.getJSON(url, function(data){  
					$.each(data,function(index,item){ 
						items+="<option value='"+item.id+"'>"+item.name+"</option>";					
					});
					$('#add-rmaorder-form #selectlocation').html(items);
					$('#add-rmaorder-form #selectlocation').select2("val", data.id);
				});
				//Loadlocations(customer, $("#add-rmaorder-form #selectLocation"), data.id);
				//load location items for customer
				$('#rma-add-location-form')[0].reset();
				new PNotify({
					title: 'Notifications',
					text: data.message,
					type: 'success',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 5000
				});						
				$('#saveLocation').modal('hide');
			}
	   });
		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	}
});	
//
$('form#o-add-location-details-form').submit(function(event) {
	event.preventDefault(); // Prevent the form from submitting via the browser
	var form = $(this);
	//alert(form.serialize());
	$.ajax({
		type: 'POST',  
		url: form.attr('action'),
		data: form.serialize(),
		dataType: "json",
		encode          : true								 
	}).done(function(data) {
		form[0].reset();						
		new PNotify({
			title: 'Notifications',
			text: 'Location Details has been successfully updated!',
			type: 'success',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});				
		$('#LocationDetailsModal').modal('hide');
		UpdateLocationSettings(data.id);
	});							
});

//load default 
function loadInventory(locationid)
{
	$.ajax({
		url: jsBaseUrl+"/inventory/loadcustomerinventory?locationid=" + locationid,
		beforeSend: function() {$("#loaded-content-inventory-category").children().prop('disabled',true); $("#loaded-content-inventory-location").children().prop('disabled',true);},
		complete: function() {             
			$('#rma-main-gridview #loading-inventory').hide(); 
			$("#loaded-content-inventory-category").children().prop('disabled', false); 
			$("#loaded-content-inventory-location").children().prop('disabled', false);			
			//
			$('[id^="load-models-location-"]').on('click', function() {
				var e = $(this);
				var locationid = e.attr('lid');
				var customerid = e.attr('pid');
				//
				$('#loaded-content-location-' + locationid).show();
				e.hide();
				$('#close-models-location-' + locationid).show();
			}); 

			$('[id^="close-models-location-"]').on('click', function() {
				var e = $(this);
				var locationid = e.attr('lid');
				$('#loaded-content-location-' + locationid).hide();
				$('#load-models-location-' + locationid).show();
				e.hide();
			});
		}
	}).done(function (data) {
		$("#loaded-content-inventory-category").html(data.html_category);
		$("#loaded-content-inventory-location").html(data.html_location);
	});	
}

$('#search-serial-overview-title').hide();

$(document).on('click', '.transferLocation', function(){
    var url = $(this).attr('href');
    $.ajax({
       url: url,
       dataType: 'JSON',
       beforeSend: function(){
           $('#loading').show();
       },
       success: function(data){
           $('#loading').hide();
           $('.inventory-details').html(data.inventory_details);
           $('#loaded-transfer-location').html(data.html);
           $('#choose_location').select2({allowClear: true});
		   $.fn.modal.Constructor.prototype.enforceFocus = function() {};
           $('#transferLoc').modal('show');
       }
    });
    return false;
});
$(document).on('click', '#save_transer_loc', function(){
    var url = $('#transfer-loc-form').attr('action');
    $.ajax({
        url: url,
        type: 'POST',
        beforeSend: function(){
          $('#loading').show();  
        },
        data: $('#transfer-loc-form').serialize(),
        success: function(){
			$('#loading').hide();
			var search = '';
			var url = '';
			
			if($('#loaded-serial-search-content .pagination li.active').legth){
				url = $('#loaded-serial-search-content .pagination li.active a').attr('data-href');
			} else {
				search = $('#searchSerial').val();
			}
			
			$('#transferLoc').modal('hide');
			
			searchSerial(search, url);
			
			new PNotify({
				title: 'Notifications',
				text: 'Item has been successfully reallocated!',
				type: 'info',
				styling: "bootstrap3",
				opacity: 0.8,
				delay: 5000
			});		  
        }
    });
    return false;
});

$(document).on("keyup", '#searchSerial', function(event) { 
	var inputContent = $(this).val();
	if(event.keyCode != 46) {
		if( (inputContent.length > 1)) {
			searchSerial(inputContent, '');	
		}
	}
});
//
$(document).on("keydown", '#searchSerial', function(event) { 
	if( (event.keyCode == 13)) {
		searchSerial($(this).val(), '');
		event.preventDefault();
		return false;
	}
});
//
$(document).on("click touchstart", '#searchSerialBtn', function(event) { 
	if($('#searchSerial').val().length===0)
		alert('Search field value missing!');
	else{
		//process order search 
		searchSerial($('#searchSerial').val(), '');	
	}
});
//
function searchSerial(query, url)
{
	var _url = url;
        if(url==''){
		_url = jsBaseUrl+"/site/serialsearch?query="+query;
        }
	$.ajax({
		url: _url,
		dataType: "json",
		//beforeSend: function() { $('#customer-inventory-home, #rmacustomerinventoryhome').hide(); $('#loading').show(); $("#loaded-serial-search-content").children().prop('disabled',true);},
		beforeSend: function() {  $('#customer-inventory-home, #rmacustomerinventoryhome').removeClass('active');$('#loading').show(); $("#loaded-serial-search-content").children().prop('disabled',true);},		
		complete: function() { 
			$('#loading').hide(); 
			$('#loaded-serial-search-content .pagination a').each(function() {
					if($(this).attr('href') != '#')
						$(this).attr('onClick', 'searchSerial("","' + $(this).attr('href') + '");');
                    $(this).attr('data-href', $(this).attr('href'));    
					$(this).attr('href', '#');
				}); 
                        //sorting
                        $('#loaded-serial-search-content thead th a').each(function() {
                            $(this).attr('onClick', 'searchSerial("","' + $(this).attr('href') + '");');
                            $(this).attr('href', '#');
                        }); 
			$("#loaded-serial-search-content thead th a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
				$(this).on("click touchstart", function(event) { 
					searchSerial('', $(this).attr('data-href'));
					event.preventDefault();
				});			
			});						
		}
	}).done(function (data) {
            $('#myTab li').removeClass('active');
            $('#myTabContent .tab-pane').removeClass('active');
            $('#loaded-serial-search-content').addClass('active');
		//alert(data);
		if (data.success) {
			$(".serial-search-count").html(data.count);
			$("#loaded-serial-search-content").html(data.html);
            $('#search-serial-overview-title').show();
            $('#rmacustomerinventorysearch, #search-serial-overview-title ').addClass('active in');
			$("#loaded-serial-search-content table").stacktable({
				myClass: 'table table-striped table-bordered'
			});		
		}
		//
		$('#loaded-serial-search-content .pagination a, #loaded-serial-search-content thead th a').click(function(){
			return false;
		});
	});	
}

$('#selectlocation').on('change', function (event) {
	if($(this).val().length != 0)
		Loadrmalocation($(this).val());
});

function confirmModifyItem(itemid, e, type)
{
	var _url;
	if(type)
		_url = jsBaseUrl+"/inventory/confirmitem?id=" + itemid + "&tagnum=" + $('#tagnum_'+itemid).val();
	else 
		_url = jsBaseUrl+"/inventory/modifyitem?id=" + itemid + "&tagnum=" + $('#tagnum_'+itemid).val();
	$.ajax({
		url: _url,
		beforeSend: function() {$('#loading').show();},
		complete: function() {             
			$('#loading').hide(); 
		}
	}).done(function (data) {
		if(data.success)
		{
			if(type)
			{
				var error = 0;
				//alert();
				if(e.hasClass('btn-default'))
				{
					if(!$('#serial_'+itemid).val() && ($('#tagnum_'+itemid).length==1 && !$('#tagnum_'+itemid).val()))
					{
						$('#serial_'+itemid).css('border' , '1px solid red');
						error++;
					}
					//
					if(!error) 
					{
						$('#serial_'+itemid).css('border' , '1px solid #DDE2E8;');
						$('#tagnum_'+itemid).css('border' , '1px solid #DDE2E8;');
						e.removeClass('btn-default');
						e.addClass('btn-success');
						$('#checkbox_'+itemid).attr('disabled', false);
						$('#serial_'+itemid).attr('readonly', true);
						$('#tagnum_'+itemid).attr('readonly', true);
						$('#modify_'+itemid).show();
					}
				}
			}
			else
			{
				$('#checkbox_'+itemid).attr('checked', false);
				$('#checkbox_'+itemid).attr('disabled', true);
				$('#confirmed_'+itemid).removeClass('btn-success');
				$('#confirmed_'+itemid).addClass('btn-default');
				$('#serial_'+itemid).attr('readonly', false);
				$('#serial_'+itemid).focus();
				e.hide();			
			}
		}
	});		
}

function Loadrmalocation(locationid)
{
   $.ajax({
		url: jsBaseUrl+"/customers/default/loadrmamodelslocation",
		beforeSend: function() {$('#loading').show();},
		data: {
			'id': locationid
		},
		complete: function() {
			$('#rma-location-items-box').show();
			$('#rma-location-add-items-box').show();
			//
			$('[id^="confirmed_"]').click(function() {
				var e = $(this);
				var itemid = e[0].id.split('_')[1];	
				confirmModifyItem(itemid, e, 1);
			});
			//
			$('[id^="modify_"]').click(function() {
				var e = $(this);
				var itemid = e[0].id.split('_')[1];	
				confirmModifyItem(itemid, e, 0);
			});
			//
			$('[id^="unserializedcheckbox_"]').click(function() {
				var e = $(this);
				var modelid = e[0].id.split('_')[1];
				$('#unserializedqtyinput_'+modelid).val(0);
				//
				if ($(this).is(':checked')) {
					$('#unserializedqtyinput_'+modelid).show();
					$('#unserializedqtyinput_'+modelid).removeAttr('disabled');
					$('#hunserializedqtyinput_'+modelid).removeAttr('disabled');
				}
				else {
					$('#unserializedqtyinput_'+modelid).hide();
					$('#unserializedqtyinput_'+modelid).addAttr('disabled');
					$('#hunserializedqtyinput_'+modelid).addAttr('disabled');
				}
			});
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {	
			$('#location-model-content').html(data.html);
			//if(parseInt(data.count) > 0) 
			$('#locationmodelsboxtitle').html("We currently show these (" + data.count + ") models at your location. Please confirm our accuracy.");
			$('#loading').hide();
		}
	});		
}
//
$(document).on("keyup", '[id^="unserializedqtyinput_"]', function() {
    var max = parseInt($(this).attr('max'));
	var quantity_entered = parseInt($(this).val());
	if(quantity_entered > max)
		$(this).parents("div").eq(0).addClass('has-error');
	else 
		$(this).parents("div").eq(0).removeClass('has-error');
});
//
$(document).on('click', '#btnAdd', function () {
	var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
		newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
		newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
	//
		newElem.find('#selectmodel_'+num).attr('id', 'selectmodel_' + newNum);
		newElem.find('#additonalmodelbqty_'+num).attr('id', 'additonalmodelbqty_' + newNum);
		newElem.find('#currentmodelselected_'+num).attr('id', 'currentmodelselected_' + newNum);
		newElem.find('#modelqty_'+num).attr('id', 'modelqty_' + newNum);
		newElem.find('#add-serial-number-box_'+num).attr('id', 'add-serial-number-box_' + newNum);
		newElem.find('#qty_unit_box_'+num).attr('id', 'qty_unit_box_' + newNum);
		newElem.find('#qty_unit_'+num).attr('id', 'qty_unit_' + newNum);
		newElem.find('#model_serialized_'+num).attr('id', 'model_serialized_' + newNum);
		newElem.find('#loaded-serial-fields_'+num).attr('id', 'loaded-serial-fields_' + newNum);
	//
		$('#entry' + num).after(newElem);	
		$('#btnDel').attr('disabled', false);
});
//
$(document).on('click', '#btnDel', function () {
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
// enable the "add" button
	$('#btnAdd').attr('disabled', false);
	return false;
});
//
$('#btnDel').attr('disabled', true);	
//
$(document).on('change', '[id^="selectmodel_"]', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];		
	$('#additonalmodelbqty_'+row).show();
	$('#modelqty_'+row).val(1);
	var rows = '';
	$('#add-serial-number-box_'+row).html('');	
	$('#currentmodelselected_'+row).html($('#selectmodel_'+row+' option:selected').text());
	$.get(jsBaseUrl+"/ajaxrequest/checkmodelserial?modelid="+e.val(), function(data){
		$('#model_serialized_'+row).val(data.serialized);
		if(parseInt($('#model_serialized_'+row).val()))
		{ 
			rows += '<div class="row row-margin"><div class="col-md-6"><input type="input" class="form-control itemserials" id="serial_'+e.val()+'_1" placeholder="Serial Number 1" name="itemserial['+e.val()+'][0]"></div><div class="col-md-6"><input type="input" class="form-control tagnumbers" id="tagnum_'+e.val()+'_1" placeholder="Tag Number 1" name="itemtagnum['+e.val()+'][0]"></div></div>';
			$('#add-serial-number-box_1').html('<div class="form-group" id="qty_unit_box_1"><p class="lead">Please provide the serial numbers for those (<span id="qty_unit_1">1</span>) units?</p></div><div id="loaded-serial-fields_1">'+rows+'</div>');
		}
	});
});
//
$(document).on('change', '[id^="modelqty_"]', function() {
	var e = $(this);
	var quantity = parseInt(e.val());
	var row = e[0].id.split('_')[1];	
	var modelid = $('#selectmodel_'+row).val();
	var _fieldnumber = 0;
	//
	if(parseInt($('#model_serialized_'+row).val()))
	{
		$('#qty_unit_box_'+row).show(); 
		var rows = '';
		if(Math.floor(quantity) == quantity && $.isNumeric(quantity)) 
		{
			for (i = 0; i < quantity; i++) { 
				_fieldnumber = i + 1;
				rows += '<div class="row row-margin"><div class="col-md-6"><input type="input" class="form-control itemserials" id="serial_'+e.val()+'_'+i+'" placeholder="Serial Number '+ _fieldnumber +'" name="itemserial['+modelid+']['+i+'][]"></div><div class="col-md-6"><input type="input" class="form-control tagnumbers" id="tagnum_'+e.val()+'_'+i+'" placeholder="Tag Number '+ _fieldnumber +'" name="itemtagnum['+modelid+']['+i+'][]"></div></div>';
			}
		}
		//$('#loaded-serial-fields_'+row).html(rows);		
		$('#add-serial-number-box_'+row).html('<div class="form-group" id="qty_unit_box_'+row+'"><p class="lead">Please provide the serial numbers for those (<span id="qty_unit_'+row+'"></span>) units?</p></div><div id="loaded-serial-fields_'+row+'">'+rows+'</div>');	
		$('#qty_unit_'+row).html(quantity);
	}
	else 
		$('#add-serial-number-box_'+row).html('');		
});
//
$('#saveTransferItem').on('click', function(event) {
	event.preventDefault(); // Prevent the form from submitting via the browser
	var $form = $('#add-rmaorder-form');	
	var error = "";
	//alert($form.serialize());
	 //$('#add-rmaorder-form').ajaxForm({url: 'server.php', type: 'post'});
	if($('.itemserials').val().length!=0 || $('.tagnumbers').val().length!=0)
	{
		$.ajax({
			type: 'POST',  
			url: jsBaseUrl+"/inventory/raddinventory",
			data: $form.serialize(),
			dataType: "json",
			encode          : true								 
		}).done(function(data) {
			if(data.html) {
				Loadrmalocation($('#selectlocation').val());
				$('[id^="additonalmodelbqty_"]').hide();
				$('[id^="qty_unit_box_"]').hide();
				$('[id^="loaded-serial-fields_"]').html('');
				$('.itemserials').val(''); 
				$('#selectmodel_1').val('');
				$('.tagnumbers').val('');
				new PNotify({
					title: 'Notifications',
					text: data.html,
					type: 'success',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 3000
				});			
			} 
			//
			if(data.html_error) {
				new PNotify({
					title: 'Notifications',
					text: data.html_error,
					type: 'error',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 3000
				});					
			}
			//
			if(data.html_warn_error || data.html_warn_merge_error) {
				if(data.html_warn_error)
					error = data.html_warn_error;
				if(data.html_warn_merge_error)
					error = data.html_warn_merge_error;
				new PNotify({
					title: 'Notifications',
					text: error,
					type: 'notice',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 3000
				});					
			}
		});	
	} else {
		new PNotify({
			title: 'Notifications',
			text: 'Serial Or Tagnumber is required for reallocate item!',
			type: 'error',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});			
	}
});
$('#resetFormButton').on('click', function() {
	$('#selectlocation').select2('val', '');
	$('#rma-location-items-box').hide();
	$('#rma-location-add-items-box').hide();
	$('#location-model-content').html('');
	$('#add-rmaorder-form')[0].reset();
});	

$(function(){
    $('[id^="loaded-content-category-"] table, [id^="loaded-content-location"] table').stacktable({
        myClass: 'table table-striped table-bordered'
    });
});