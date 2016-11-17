$(document).ready(function () {
	$.ajax({
		url: jsBaseUrl+"/receiving/loadcustomerinventory",
		dataType: "json",
		beforeSend: function() {$('#loading').show();},
		complete: function() {
			$('[id^="order-status-popover_"]').on('click', function() {
				var e=$(this);
				var html = e.data('content');
				//alert(html.length);
				if(html.length == 0) 
				{
					$.ajax({ 
						url: e.data('poload'),
						dataType: "json",
						beforeSend: function() {e.popover().popover('hide'); $('#loading').show(); e.prop('disabled',true);},
						success: function(data) {
							if(data.success)
							{
								e.attr('data-content', data.html); 
								$('#loading').hide(); 
								e.prop('disabled',false);
								e.popover().popover('show');                                                                 
							}
						}
					});
				}
			});				
		}
	}).done(function (data) {
		if (data.success) {
			$(".load-customer-inventory").html(data.html);
			$('#loading').hide(); 
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $(".load-customer-inventory").stacktable({
                                myClass: 'table table-striped table-bordered'
                            });
                        });                        
		}
	});	
	//
	$.ajax({
		url: jsBaseUrl+"/receiving/loadincomingpurchase",
		beforeSend: function() {$('#loading').show();},
		dataType: "json",
		complete: function() {
			$('.load-incomingpurchase-content [id^="item-popover_"]').on('click', function() {
				var e=$(this);
				var html = e.data('content');
				//alert(html.length);
				if(html.length == 0) 
				{
					$.ajax({ 
						url: e.data('poload'),
						dataType: "json",
						beforeSend: function() {e.popover().popover('hide'); $('#loading').show(); e.prop('disabled',true);},
						success: function(data) {
							if(data.success)
							{
								e.attr('data-content', data.html); 
								$('#loading').hide(); 
								e.prop('disabled',false);
								e.popover().popover('show'); 
							}
						}
					});
				}
			});					
		}
	}).done(function (data) {
		if (data.success) {
			$(".load-incomingpurchase-content").html(data.html);
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $(".load-incomingpurchase-content").stacktable({
                                myClass: 'table table-striped table-bordered'
                            });
                        });
			$('#loading').hide(); 
		}
	});	
});	
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
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#detaisOfReceivingReceive").stacktable({
                                myClass: 'table table-striped table-bordered'
                            });
                        });
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
function multiSearchOr(text, searchWords){
    var length = searchWords.length;
    var index = -1;
    for(var i=0;i<length;i++){
        if(text.toLowerCase().indexOf(searchWords[i].toLowerCase()) >=0){
            index = 1;
            break;
        }
    }
  return (index>=0)?true:false;
}

function exactMatchingResults(text, searchValue){
   searchValue = searchValue.trim();
   var searchWords = searchValue.split(" ");    
   var length = searchWords.length;
    var index = -1;
    
    for(var i=0;i<length;i++){
        if(text.toLowerCase().indexOf(searchWords[i].toLowerCase()) >=0){
            index = (index === -1)?0:index;
            index++;
        }
        
        var searchString = '\\b'+searchWords[i]+'\\b';
        var regex = new RegExp(searchString);
        var match = text.match(regex);
         if(match != null){
            index++;
        }
        var searchString = '\\b'+searchWords[i].toLowerCase()+'\\b';
        var regex = new RegExp(searchString, "i");
        var match = text.toLowerCase().match(regex);
         if(match != null){
            index++;
        }     
          
    } 
    return index;
}
//
$(document).ready(function () {
    $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
        $("#receivingrir-loaded-content table").stacktable({
            myClass: 'table table-striped table-bordered'
        });
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