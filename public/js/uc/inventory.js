/*
//pagination fix
$(document).on('click', '#inventory-main-gridview .pagination a', function() {
	loadInventory($(this).attr('data-href'));
	return false;
});
//
loadInventory();
function loadInventory(url="")
{
	if(url.length == 0)
		url = jsBaseUrl+"/inventory/load";
	$('#loading').show(); 
	$("#inventory-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,
		//dataType: "json",
		//encode          : true,			
		complete: function() {
			//
			$("#inventory-loaded-content").find('script').remove();
			//pagination 
			$('#inventory-loaded-content .pagination a').each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			}); 
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};
			//$(this).find('script').remove();
			//Do whatever you want here              
			$('#loading').hide(); 
			$("#inventory-loaded-content").children().prop('disabled',false);		
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
			//
			$('[id^="item-popover_"]').hover(function() {
				var e=$(this);
				e.off('hover');
				$.get(e.data('poload'),function(d) {
					e.attr('data-content', d);
					//e.attr('title', e.attr('data-caption'));
					//alert(d);
					hideAllPopovers();
					e.popover().popover('show'); 
				});
			});
			//
			$('[id^="item-popover_"]').on( "mouseleave", function() {
				hideAllPopovers();
			});
			  
			$('[id^="item-popover_"]').focusout(function() {
				hideAllPopovers();
			});	
			//
			$('[id^="partitem-popover_"]').hover(function() {
				var e=$(this);
				e.off('hover');
				$.get(e.data('poload'),function(d) {
					e.attr('data-content', d);
					//e.attr('title', e.attr('data-caption'));
					//alert(d);
					hideAllPopovers();
					e.popover().popover('show'); 
				});
			});
			//
			$('[id^="partitem-popover_"]').on( "mouseleave", function() {
				hideAllPopovers();
			});
			  
			$('[id^="partitem-popover_"]').focusout(function() {
				hideAllPopovers();
			});
			//reorder button
			$('.showreorder').on('click', function(event) {
				//alert('clicked');
				var id = $(this)[0].id;
				var arr = id.split('||');
				//alert (arr[0]);
				$('input[name=ritem_id]').val(arr[0]);
				$('#r_item_name').html(arr[1]);
				$('#rqty').val(arr[2]); 
				$('#ReOrder').modal('toggle');
			});		
		}
	}).done(function(data) {
		//alert(data.toSource());
		$("#inventory-loaded-content").html(data);
	}).fail(function(data) {
		// Optionally alert the user of an error here...
	});	
}
//
$('#searchInventoryBtn').on('click', function(event) {
	if($('#searchInventory').val().length===0)
		alert('Search field value missing!');
	else{
		//hide list gridview
		$('#inventory-main-gridview').hide();
		$("#inventory-loaded-content").html('');
		//show search gridview
		$('#inventory-search-gridview').show();		
		//process order search 
		searchInventory($('#searchInventory').val());	
	}
});	
//
$('#searchInventory').keyup(function(event){
	var inputContent = $(this).val();
	if(event.keyCode != 46) {
		if( (inputContent.length > 1)) {
			//hide list gridview
			$('#inventory-main-gridview').hide();
			$("#inventory-loaded-content").html('');
			//show search gridview
			$('#inventory-search-gridview').show();	
			//alert(inputContent);
			//process inventory search 
			searchInventory(inputContent);	
		}
	}
	else if(inputContent=="")
		loadInventory();
	else
		event.preventDefault();
});
//
$('#searchInventory').keydown(function(event){
	if( (event.keyCode == 13)) {
		//hide list gridview
		$('#inventory-main-gridview').hide();
		$("#inventory-loaded-content").html('');
		//show search gridview
		$('#inventory-search-gridview').show();		
		//process inventory search 
		searchInventory($(this).val());	
		//
		event.preventDefault();
		return false;
	}
});
//
function searchInventory(query)
{
	$.ajax({
		url: jsBaseUrl+"/inventory/search",
		data: {
			query: query
		},
		dataType: "json",
		beforeSend: function() { $('#loading').show(); $("#inventory-loaded-content-search").children().prop('disabled',true);},
		complete: function() { 
			$("#inventory-loaded-content").find('script').remove();
			//pagination 
			// $('#inventory-search-gridview .pagination a').each(function() {
				// if($(this).attr('href') != '#')
					// $(this).attr('data-href', $(this).attr('href'));
				// $(this).attr('href', '#');
			// }); 
			// $('#inventory-search-gridview .pagination a').click(function() {
				// loadInventory($(this).attr('data-href'));
				// return false;
			// });
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};		
			//
			$('#loading').hide(); 
			$("#inventory-loaded-content-search").children().prop('disabled',false);
			//
			$('[id^="item-popover_"]').hover(function() {
				var e=$(this);
				e.off('hover');
				$.get(e.data('poload'),function(d) {
					e.attr('data-content', d);
					//e.attr('title', e.attr('data-caption'));
					//alert(d);
					hideAllPopovers();
					e.popover().popover('show'); 
				});
			});
			//
			$('#inventory-search-gridview [id^="item-popover_"]').on( "mouseleave", function() {
				hideAllPopovers();
			});
			  
			$('#inventory-search-gridview [id^="item-popover_"]').focusout(function() {
				hideAllPopovers();
			});	
			//
			$('#inventory-search-gridview [id^="partitem-popover_"]').hover(function() {
				var e=$(this);
				e.off('hover');
				$.get(e.data('poload'),function(d) {
					e.attr('data-content', d);
					//e.attr('title', e.attr('data-caption'));
					//alert(d);
					hideAllPopovers();
					e.popover().popover('show'); 
				});
			});
			//
			$('#inventory-search-gridview [id^="partitem-popover_"]').on( "mouseleave", function() {
				hideAllPopovers();
			});
			  
			$('#inventory-search-gridview [id^="partitem-popover_"]').focusout(function() {
				hideAllPopovers();
			});			
		}
	}).done(function (data) {
		//alert(data);
		if (data.success) {
			$("#inventory-results-count").html(data.count);
			$("#inventory-loaded-content-search").html(data.html);
		}
	});	
	//e.preventDefault();
}
*/
$('#searchCustomer').typeahead({
	name: 'Customers',
	onSelect: function(item) {
		//$('').val();
		loadCustomerModels(item.value, '');
	},				
	ajax: jsBaseUrl+"/ajaxrequest/listcustomersinventory?query="+$('#searchCustomer').val(),
	items : 10	
});

$("#searchCustomer").click(function(event){
    event.stopPropagation();
});

$(document).on("click", '#transfer_inventory', function(){
    var url = $(this).attr('href');
    $.ajax({
        url: url,
        dataType: 'JSON',
        beforeSend: function(){
            $('#transferInv .quantity_error').hide();
            $('#loading').show();
        },
        success: function(data){
            $('#transfer_modal_name').html(data.model);
            $('#transfer_items_loaded').html(data.html);
            
            $('.transfer_customer_name').each(function(){
                var thisObj = $(this);
                thisObj.typeahead({
                    onSelect: function(item) {
                        thisObj.prev().val(item.value);
                    },			
                    ajax: jsBaseUrl+"/ajaxrequest/listcountries"
                });                
            });
            $('#transferInv').modal('show');
        },
        complete: function(){
            $('#loading').hide();
        }
    });
    return false;
});

$(document).on("click", ".invent_transfer", function(){
    var thisObj = $(this);
    var form = $('#transfer-inventory-form');
    var error = false;
    var totalQty = 0;
    var maxQty = 0;
    form.find('input, select').each(function(){
        if(!$(this).val()){
            error = true;
            $(this).css({border: '1px solid #d43f3a'});
        } else {
            $(this).css({border: ''});
        }
        if($(this).hasClass('transfer_quantity')){
            totalQty += parseInt($(this).val());
            maxQty = $(this).attr('max');
        }
    });
    if(maxQty != totalQty){
        $('#transferInv .total_quantity').html(maxQty);
        new PNotify({
            title: 'Notifications',
            text: 'Total Quantity should not be Greater/Less than ' + maxQty,
            type: 'error',
            styling: "bootstrap3",
            opacity: 0.8,
            delay: 5000
	});
    } else {
        if(!error){
            thisObj.addClass('disabled');
            $.ajax({
                url: form.attr('action'),
                dataType: 'JSON',
                data: form.serialize(),
                type: 'POST',
                beforeSend: function(){
                    thisObj.unbind('click');
                    $('#loading').show();
                    $('#transfer-inventory-form input,#transfer-inventory-form select, #transfer-inventory-form a').prop('disabled', true);
                },
                success: function(data){
                    thisObj.bind('click');
                    $('#transfer_modal_name').html(data.model);
                    $('#transfer_items_loaded').html(data.html);
                    $('#transfer-inventory-form input, #transfer-inventory-form select, #transfer-inventory-form a').prop('disabled', false);
                     new PNotify({
                        title: 'Notifications',
                        text: 'Inventory has been transferred successfully.',
                        type: 'success',
                        styling: "bootstrap3",
                        opacity: 0.8,
                        delay: 5000
                    });
                },
                complete: function(){
                    $('#loading').hide();
                    thisObj.removeClass('disabled');
                }
            });
    }
    }
    return false;
});
//pagination fix
//$(document).on('click', '#inventory-main-gridview .pagination a', function() {
//	loadInventory($(this).attr('data-href'));
//	return false;
//});
//
$(document).on("click touchstart", '#searchInventoryBtn', function(event) { 
	if($('#searchInventory').val().length===0)
		alert('Search field value missing!');
	else{
		//hide list gridview
		$('#inventory-main-gridview').hide();
		$("#inventory-loaded-content").html('');
		//show search gridview
		$('#inventory-search-gridview').show();		
		//process order search 
		searchInventory($('#searchInventory').val());	
	}
});	
//
$(document).on('click', '.showreorder', function(event) {
	var id = $(this)[0].id;
	var arr = id.split('||');
	//alert (arr[0]);
	$('input[name=ritem_id]').val(arr[0]);
	$('#r_item_name').html(arr[1]);
	$('#rqty').val(arr[2]); 
	$('#ReOrder').modal('toggle');
});	

$('#show-inventory-order').on('click', function (){
	$('#selectlocation').val('');
	$('.warehouseorder').attr('disabled', false);	
	$('#inventory-customer-new-order').show();
});

$(document).on('change', '[id^=item-order-qty]', function (){
	var e = $(this);
	var row = e[0].id.split('_')[1];
	$.ajax({
		url: jsBaseUrl+"/inventory/default/saveworderquantity",
		beforeSend: function() {
			$('#loading').show();
		},
		data: {
			id: row,
			quantity: e.val()
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#loaded-order-item-content").html('<div id="item-added-'+row+'">'+data.html+'</div>');
			$('#loading').hide();
		}
	});		
});

$(document).on("click", '#resetFormButton', function(event) { 
	//reset location
	$('#selectlocation').select2('val', '');
	//clear old entries
	$.ajax({
		url: jsBaseUrl+"/inventory/default/resetitemtoorder",
		beforeSend: function() {
			$('#loading').show();
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#loaded-order-item-content").html(data.html);
			$('#loading').hide();
		}
	});		
});
$(document).on("click", '[id^=remove-item-from-order]', function(event) { 
	var e = $(this);
	var row = e[0].id.split('_')[1];
	//alert(row);
	$.ajax({
		url: jsBaseUrl+"/inventory/default/removeitemtoorder",
		beforeSend: function() {
			$('#loading').show();
		},
		data: {
			id: row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#loaded-order-item-content").html('<div id="item-added-'+row+'">'+data.html+'</div>');
			$('#loading').hide();
		}
	});		
});

var items="";
var url = jsBaseUrl+"/ajaxrequest/listrepresentativelocations?customer=178";
$.getJSON(url, function(data){  
	$.each(data,function(index,item){ 
		items+="<option value='"+item.id+"'>"+item.name+"</option>";					
	});
	$('#selectlocation').html(items); 								
});

function addItemToWarehouseOrder(id)
{
	$.ajax({
		url: jsBaseUrl+"/inventory/default/additemtoorder",
		beforeSend: function() {
			$('#loading').show();
		},
		data: {
			id: id
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {
			$("#loaded-order-item-content").html('<div id="item-added-'+id+'">'+data.html+'</div>');
			$('#loading').hide();
		}
	});		
}
//
loadInventory("");
function loadInventory(url)
{
	if(url == "")
		url = jsBaseUrl+"/inventory/load";
	$('#loading').show(); 
	$("#inventory-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,
                beforeSend: function(){
                  $("#inventory-loaded-content").html('');
                  $("#inventory-panel .x_title span").html('All');
                },
		complete: function() {
			//$("#inventory-loaded-content-search").find('script').remove();
			//pagination 
                        $('#inventory-loaded-content .pagination a, #inventory-loaded-content th a').each(function() {
                             $(this).attr('onClick', 'loadInventory("' + $(this).attr('href') + '");');
                             $(this).attr('href', 'javascript:void(0);');
			});
                        $('[id^="item-popover_"], [id^="partitem-popover_"]').on('click', function() {
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
			//
			$('#loading').hide(); 
			$("#inventory-loaded-content").children().prop('disabled',false);				
		}
	}).done(function(data) {
		$("#inventory-loaded-content").html(data);
                $('#inventory-panel').show();
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#inventory-loaded-content table").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
	});	
}

function loadIAssembly(url)
{
	if(url == "")
		url = jsBaseUrl+"/assembly/load";
	$('#loading').show(); 
	$("#iassembly-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,
                beforeSend: function(){
                    $("#iassembly-loaded-content").html('');
                },
		//dataType: "json",
		//encode          : true,			
		complete: function() {
			//
			//$("#iassembly-loaded-content").find('script').remove();
			//pagination 
//			$('#iassembly-loaded-content .pagination a').each(function() {
//				$(this).attr('data-href', $(this).attr('href'));
//				$(this).attr('href', '#');
//			}); 
                        $('#iassembly-loaded-content .pagination a, #iassembly-loaded-content th a').each(function() {
                             $(this).attr('onClick', 'loadIAssembly("' + $(this).attr('href') + '");');
                             $(this).attr('href', 'javascript:void(0);');
			});
			//
//			$(document).on('click', '#inventory-main-gridview .pagination a', function() {
//				loadIAssembly($(this).attr('data-href'));
//				return false;
//			});
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};
			//$(this).find('script').remove();
			//Do whatever you want here              
			$('#loading').hide(); 
			$("#iassembly-loaded-content").children().prop('disabled',false);		
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
			//
			//$.getScript(jsBaseUrl+"/public/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
		}
	}).done(function(data) {
		$("#iassembly-loaded-content").html(data);
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#iassembly-loaded-content table").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
	});	
}

/*function loadICustomer(url)
{
	if(url == "")
		url = jsBaseUrl+"/customers/iload";
	$('#inventory-main-gridview #loading').show(); 
	$("#icustomer-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,		
		complete: function() {
			//
			$("#icustomer-loaded-content").find('script').remove();
			//pagination 
			$('#icustomer-loaded-content .pagination a').each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			});
			$('#icustomer-loaded-content').on('click', '.pagination a', function() {
				loadICustomer($(this).attr('data-href'));
				return false;
			});		
			//             
			$('#inventory-main-gridview #loading').hide(); 
			$("#icustomer-loaded-content").children().prop('disabled',false);	
			$(document).on('click', '#icustomer-loaded-content #selectICustomer', function() {
				alert('hit!');
			});				
			$('#icustomer-loaded-content').on('change', "#selectICustomer", function() {
				loadCustomerModels($(this).val(), "");
			});
			//
			//$('#selectICustomer').chosen();
		}
	}).done(function(data) {
		$("#icustomer-loaded-content").html(data);
	});	
}*/

function loadICategory(url)
{
	if(url == "")
		url = jsBaseUrl+"/category/load";
	$('#loading').show(); 
	$("#icategory-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,		
		complete: function() {
			//
			//$("#icategory-loaded-content").find('script').remove();
			//pagination 
			$('#icategory-loaded-content .pagination a').each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			});
			$('#icategory-loaded-content').on('click', '.pagination a', function() {
				loadICategory($(this).attr('data-href'));
				return false;
			});		
			//             
			$('#loading').hide(); 
			$("#icategory-loaded-content").children().prop('disabled',false);		
		}
	}).done(function(data) {
		$("#icategory-loaded-content").html(data);
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#icategory-loaded-content table").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
	});	
}

function loadCategoryModels(idcategory, url, category)
{
	if(url == "")
		url = jsBaseUrl+"/category/loadmodels?idcategory="+idcategory;
	$('#loading').show(); 
	$("#inventory-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,
                beforeSend: function(){
                  $("#inventory-loaded-content").html('');
                  $("#inventory-panel .x_title span").html(' Category: '+category);
                  $('#myTabContent .tab-pane').removeClass('active');
                  $('#inventoryhome').addClass('active in');
                },                
		complete: function() {
			//
			$("#inventory-loaded-content").find('script').remove();
			//pagination 
			$('#inventory-loaded-content .pagination a').each(function() {
                            $(this).attr('onClick', 'loadCategoryModels('+idcategory+',"' + $(this).attr('href') + '","'+category+'");');
                             $(this).attr('href', 'javascript:void(0);');
//				$(this).attr('data-href', $(this).attr('href'));
//				$(this).attr('href', '#');
			});
//			$('#inventory-loaded-content').on('click', '.pagination a', function() {
//				loadCategoryModels(idcategory, $(this).attr('data-href'));
//				return false;
//			});		
			//
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
                        $('[id^="item-popover_"], [id^="partitem-popover_"]').on('click', function() {
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
			//
			//$.getScript(jsBaseUrl+"/public/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});			
			//             
			$('#loading').hide(); 
			$("#inventory-loaded-content").children().prop('disabled',false);		
		}
	}).done(function(data) {
		$("#inventory-loaded-content").html(data);
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#inventory-loaded-content table").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
	});	
}
$('#inventory-main-gridview .dropdown-menu li').click(function(){
    $('#inventory-main-gridview #myTab li').removeClass('active');
    $(this).parents('li').addClass('active');
});

function loadCustomerModels(idcustomer, url, customer)
{
	if(url == "")
		url = jsBaseUrl+"/customers/loadmodels?idcustomer="+idcustomer;
	$('#loading').show(); 
	$("#inventory-loaded-content").children().prop('disabled',true);
	$.ajax({
		url: url,
                beforeSend: function(){
                    $("#inventory-loaded-content").html('');
                    $("#inventory-panel .x_title span").html(' Customer: '+customer);
                    $('#myTabContent .tab-pane').removeClass('active');
                    $('#inventoryhome').addClass('active in');
                }, 
		complete: function() {
			//
			$("#inventory-loaded-content").find('script').remove();
			//pagination 
			$('#inventory-loaded-content .pagination a, #inventory-loaded-content th a').each(function() {
                             $(this).attr('onClick', 'loadCustomerModels('+idcustomer+',"' + $(this).attr('href') + '","'+customer+'");');
                             $(this).attr('href', 'javascript:void(0);');
			//	$(this).attr('data-href', $(this).attr('href'));
			//	$(this).attr('href', '#');
			});
//			$('#inventory-loaded-content').on('click', '.pagination a', function() {
//				loadCustomerModels(idcustomer, $(this).attr('data-href'), customer);
//				return false;
//			});	
			//
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
			//
                        $('[id^="item-popover_"], [id^="partitem-popover_"]').on('click', function() {
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
			//$.getScript(jsBaseUrl+"/public/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});			
			//             
			$('#loading').hide(); 
			$("#inventory-loaded-content").children().prop('disabled',false);		
		}
	}).done(function(data) {
		$("#inventory-loaded-content").html(data);
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#inventory-loaded-content table").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
                $("#inventory-panel .x_title span").html(' Customer: '+customer);
	});	
}
//
$(document).on("keyup", '#searchInventory', function(event) { 
	var inputContent = $(this).val();
	if(event.keyCode != 46) {
		if( (inputContent.length > 1)) {
			//hide list gridview
			$('#inventory-main-gridview').hide();
			$("#inventory-loaded-content").html('');
			//show search gridview
			$('#inventory-search-gridview').show();	
			//alert(inputContent);
			//process inventory search 
			searchInventory(inputContent);	
		}
	}
	else if(inputContent=="")
		loadInventory("");
	else
		event.preventDefault();
});
$(document).on("click", "#soft_delete_inventory", function(){
    var delHref = $(this).attr('href');
    $('#deleteConfirm').modal('show');
    $('#yes-delete-order').attr('href', delHref);
    return false;    
});
//
$(document).on("keydown", '#searchInventory', function(event) { 
	if( (event.keyCode == 13)) {
		//hide list gridview
		$('#inventory-main-gridview').hide();
		$("#inventory-loaded-content").html('');
		//show search gridview
		$('#inventory-search-gridview').show();		
		//process inventory search 
		searchInventory($(this).val());	
		//
		event.preventDefault();
		return false;
	}
});
$(document).on("keyup", "#transfer-qty" , function(){
    var maxQuantity = parseInt($(this).attr('max'));
    var quantity = parseInt($(this).val());
    if(quantity > maxQuantity){
        $(this).val(maxQuantity);
    } else if($(this).val() == ''){
        $(this).val('1');
    }
});
$(document).on("click", "#transfer_items_loaded .panel_toolbox .collapse-link", function(){
    $(this).parents('.x_panel').find('.x_content').slideToggle('slow');
});
$(document).on("click", "#transfer_items_loaded .panel_toolbox .close-link", function(){
    $(this).parents('.x_panel').remove();
});
function searchPInventory(url)
{
    $('.mobile-menu').hide();
	$.ajax({
		url: url,
		dataType: "json",
		beforeSend: function() { $('#loading').show(); $("#inventory-loaded-content-search").children().prop('disabled',true);},
		complete: function() { 
			$("#inventory-loaded-content-search").find('script').remove();
			//pagination 
			$('#inventory-loaded-content-search .pagination a').each(function() {
				if($(this).attr('href') != '#')
					$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			}); 
			$.getScript(jsBaseUrl+"/public/js/bxslider/jquery.bxslider.min.js", function( data, textStatus, jqxhr ) {});
			/*$('#inventory-loaded-content-search .pagination a').click(function() {
				searchPInventory($(this).attr('data-href'));
				return false;
			});*/
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};		
			//
			$('#loading').hide(); 
			$("#inventory-loaded-content-search").children().prop('disabled',false);			
		}
	}).done(function (data) {
		//alert(data);
		if (data.success) {
			$("#inventory-results-count").html(data.count);
			$("#inventory-loaded-content-search").html(data.html);
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#inventory-loaded-content-search table").stacktable({
                                    myClass: 'table table-striped table-bordered'
                            });
                        });                        
		}
	});	
	//e.preventDefault();
}
$(document).ready(function() {
	$.getScript("http://maxcdn.bootstrapcdn.com/bootstrap/3.3.6/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
	//load tooltip
	$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
	//load popover
	$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});	
	//load bxslider
	$.getScript(jsBaseUrl+"/public/js/bxslider/jquery.bxslider.min.js", function( data, textStatus, jqxhr ) {});
        $(document).on('click','#inventory-main-gridview .dropdown-menu li',function(){
            $('#inventory-main-gridview .dropdown-menu li').removeClass('active');
            $(this).addClass('active');
        });
});

function searchInventory(query)
{
    $('.mobile-menu').hide();
	var _url = jsBaseUrl+"/inventory/search?query="+query;
	if(query.indexOf("?query=")>=0)
		_url = query;
	//alert(_url);
	$.ajax({
		url: _url,
		dataType: "json",
		beforeSend: function() { $('#loading').show();},
		success: function(data) {
			if (data.success) {
				$('#loading').hide();
				$("#inventory-results-count").html(data.count);
				$("#inventory-loaded-content-search").html(data.html);
			}						  
		},
                complete: function(data){
                        $('[id^="item-popover_"], [id^="partitem-popover_"]').on('click', function() {
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
                        $('#inventory-search-gridview #myTab li, #inventorysearch').addClass('active');
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#inventory-loaded-content-search table").stacktable({
                                    myClass: 'table table-striped table-bordered'
                            });
                        });                        
                }
	});
}

$(document).on('click touchstart', '#inventory-search-gridview .pagination a', function() {
	searchInventory($(this).attr('href'));
	return false;
});
//
function removeAssembly(id)
{
	if(confirm('are you sure to delete this order ?'))
	{
		$.ajax({ 
			url: jsBaseUrl+"/assembly/delete",
			data: {
				id: id
			},
			dataType: "json"
		}).done(function (data) {
			if (data.success) {
				loadIAssembly("");
				/*$('#inventoryassemblies #assembly-msg').html(data.message);
				$('#inventoryassemblies #assembly-msg').delay(2000).fadeOut("slow", function () { 
					$('#inventoryassemblies #assembly-msg').hide(); 
					$('#inventoryassemblies #assembly-msg').html('');
				});*/
			}
		});
		return true;
	}
	return false;
}

function deletedInventory(url)
{
    if(url == ''){
        url = jsBaseUrl+"/inventory/getdeleted";
    }
	$.ajax({
		url: url,
		beforeSend: function() {$('#loading').show();$('#inventorydelete').hide()},
        dataType: "json",
		complete: function() {
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};
			//Do whatever you want here              
			$('#loading').hide(); 	
			//pagination 
			$("#inventory-deleted-content .pagination a , #inventory-deleted-content th a").each(function() {
				$(this).attr('onClick', 'deletedInventory("' + $(this).attr('href') + '");');
                                $(this).attr('href', 'javascript:void(0);');
			});	
			//		
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});


			$('[id^="item-popover_"]').add('[id^="inventory-status-popover_"]').on('click', function() {
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
			
			$('#myTab li').removeClass('active');
			
			$('#inventory-deleted-tab').addClass('active');
			
			$('#myTabContent .tab-pane').removeClass('active');
			
			$('#myTabContent #inventorydelete').addClass('active in');
		}
	}).done(function (data) {
		if(data.success){
			$("#inventory-deleted-content").html(data.html);
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#inventory-deleted-content table").stacktable({
                                    myClass: 'table table-striped table-bordered'
                            });
                        });
			$('.delete_count').html(data.count);
			$('#inventorydelete').show();
                        $(".revertInventory").click(function(){
				var href = $(this).attr('href');
				$.ajax({
					url: href,
					success: function(data){
						if(data){
						    deletedInventory('');
						} 
					}
				});
				return false;
			});
            }
	});	  
}

$('form#add-warehouse-order-form').submit(function(event) {
	var error = 0;
	if(!$('#selectlocation').val())
	{
		new PNotify({
			title: 'Notifications',
			text: 'Please select a location before creating your order.',
			type: 'error',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});	
		error++;
	} else if ($(".modelqty").length == 0) {
		new PNotify({
			title: 'Notifications',
			text: 'Please add(+) an item before proceed to order creation.',
			type: 'error',
			styling: "bootstrap3",
			opacity: 0.8,
			delay: 5000
		});			
		error++;	
	}
	if(error)
		event.preventDefault();
	
});