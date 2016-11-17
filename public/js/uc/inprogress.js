$(document).on("click", "#soft_delete_order, #soft_delete_qorder", function(){
    var delHref = $(this).attr('href');
    $('#deleteConfirm').modal('show');
    $('#yes-delete-order').attr('href', delHref);
    return false;    
});
$(document).on("click touchstart", '#all-cleaned-button', function(event) { 
	$('#CleaningAllConfirmation').modal('toggle');
});
//
$(document).on("click touchstart", "#confirmAllButton", function() {
	var _url = jsBaseUrl+"/inprogress/turnalltocleaning?id="+getUrlParameter('id');
	$.ajax({
		url: _url,
		dataType: 'json',
		success: function(data) {
		}
	}).done(function (data) {
		if(data.success) {
			$('#all-cleaned-button').hide();
			$('#CleaningAllConfirmation').modal('hide');
			$('.cleaningitem').attr('disabled', true);
			$('.cleaningitem').html('Cleaned');
			//
			if(data.requiretesting)
			{
				$.each(data.requiretesting, function (index, value) {
					//alert('#inprogressdetailsall #testing-item_'+value);
					$('#inprogressdetailsall #testing-item_'+value).attr('disabled', false);
				});
				/*$("#inprogressdetailsall .testingitem").each(function() {
					var p = $(this);
					var item = p[0].id.split('_')[1]; 
					//alert(item);
					//alert(data.requiretesting);
				});*/
			}
			
			if(data.readytoship)
			{
				$.each(data.readytoship, function (index, value) {
					$('#inprogressdetailsall #ready-item-'+value).attr('disabled', false);
				});				
			}			
			//
			new PNotify({
						title: 'Notifications',
						text: 'Items has been successfully cleaned!',
						type: 'success',
						styling: "bootstrap3",
						opacity: 0.8,
						delay: 5000
					});
		}
	});		
});
$(document).on("click touchstart", '#searchOrderBtn', function(event) { 
	if($('#searchOrder').val().length===0)
		alert('Search field value missing!');
	else{
		//hide list gridview
		$('#order-main-gridview').hide();
		//show search gridview
		$('#order-search-gridview').show();		
		//process order search 
		searchOrder($('#searchOrder').val(), '');	
	}
});
//
$(document).on("keyup", '#searchOrder', function(event) { 
	var inputContent = $(this).val();
	if(event.keyCode != 46) {
		if( (inputContent.length > 1)) {
			//hide list gridview
			$('#order-main-gridview').hide();
			//$("#inventory-loaded-content").html('');
			//show search gridview
			$('#order-search-gridview').show();	
			//alert(inputContent);
			//process inventory search 
			searchOrder(inputContent, '');	
		}
	}
});
//
$(document).on("keydown", '#searchOrder', function(event) { 
	if( (event.keyCode == 13)) {
		//hide list gridview
		$('#order-main-gridview').hide();
                
		//show search gridview
		$('#order-search-gridview').show();
                $('#myTabContent .tab-pane').removeClass('active');
                $('#myTabContent .order-all').parent().addClass('active');
		//process order search 
		searchOrder($(this).val(), '');	
		//
		event.preventDefault();
		return false;
	}
});
function searchOrder(query, url)
{
	var _url;
//	if(query=="")
//		_url = url;
//	else
		_url = jsBaseUrl+"/inprogress/search?query="+query;
	$.ajax({
		url: _url,
		dataType: "json",
		beforeSend: function() { $('#loading').show(); $("#order-loaded-content-search").children().prop('disabled',true);},
		complete: function() { 
			$('#loading').hide(); 
			$("#order-loaded-content-search").children().prop('disabled',false);
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};
			//pagination 
			$("#order-loaded-content-search .pagination a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			});	
			$(document).on('click', '#order-search-gridview .pagination a', function() {
				searchOrder('', $(this).attr('data-href'));
				return false;
			});				
			//sorting
			$("#order-loaded-content-search thead th a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
				$(this).on("click touchstart", function(event) { 
					searchOrder('', $(this).attr('data-href'));
					event.preventDefault();
				});			
			}); 			
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
			
			$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
				
			//
			$('.showCustomer').on("click", function() {
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
			
			$('[id^="order-status-popover_"], [id^="item-popover_"]').click(function() {
                            var e=$(this);
                            var html = e.data('content');
                            //alert(html.length);
                            if(!html) 
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
//			$('[id^="item-popover_"]').on( "mouseleave", function() {
//				hideAllPopovers();
//			});
//			
//			$('[id^="item-popover_"]').focusout(function() {
//				hideAllPopovers();
//			});				
		}
	}).done(function (data) {
            $('#ordersearch').addClass('active');
		//alert(data);
		if (data.success) {
			$("#order-results-count").html(data.count);
			$("#order-loaded-content-search").html(data.html);
		}
	});	
	//e.preventDefault();
}
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

function searchSerial(query, url)
{
	var _url = url;
        if(url==''){
		_url = jsBaseUrl+"/inprogress/serialsearch?query="+query+"&idorder="+getUrlParameter('id');
        }
	$.ajax({
		url: _url,
		dataType: "json",
		beforeSend: function() {  $('#inprogress-details-all, #inprogressdetailsall').removeClass('active');$('#loading').show(); $("#loaded-serial-search-content").children().prop('disabled',true);},		
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
		if (data.success) {
			$("#rma-main-gridview #serial-search-count").html(data.count);
			$("#loaded-serial-search-content").html(data.html);
            $('#search-serial-overview-title').show();
            $('#serialitemssearch, #search-serial-overview-title').addClass('active in');
		}
		$('#loaded-serial-search-content .pagination a, #loaded-serial-search-content thead th a').click(function(){
			return false;
		});
	});	
}
/**
 *
 */
function deleteOrders(url, customerid)
{
	var __url = url;
	if(__url=="")
		__url = jsBaseUrl+"/inprogress/getdeleted?customer=" + customerid;
	$.ajax({
		url: __url,
		beforeSend: function() {$('#loading').show();$('#orderdelete').hide()},
        dataType: "json",
		complete: function() {
			//Do whatever you want here              
			$('#loading').hide(); 	
			//pagination 
			$("#order-deleted-content .pagination a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
				$(this).on("click touchstart", function(event) { 
					deleteOrders($(this).data('href'), customerid);
					event.preventDefault();
					return false;
				});
			});	
			//sorting
			$("#order-deleted-content thead th a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
				$(this).on("click touchstart", function(event) { 
					deleteOrders($(this).data('href'), customerid);
					event.preventDefault();
					return false;
				});			
			}); 			
			//
			$(".revertOrder").click(function(){
				var href = $(this).attr('href');
				$.ajax({
					url: href,
					success: function(data){
						if(data){
							//alert('hit!');
						    deleteOrders('', customerid);
						    $('.delete_count').html(data);
							//
							/*new PNotify({
								title: 'Notifications',
								text: 'Order has been successfully reverted!',
								type: 'info',
								styling: "bootstrap3",
								opacity: 0.8,
								delay: 5000
							});	*/						
						} 
					} 
				});
				return false;
			});
			//
			$(".deleteOrder").click(function(){
				var href = $(this).attr('href');
				$('#deleteConfirm').modal('show');
				$('#yes-delete-order').attr('href', href);
				return false;
			});		
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});

			$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});

			$('[id^="item-popover_"]').add('[id^="order-status-popover_"]').on('click', function() {
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
			
			//$('#myTab li').removeClass('active');
			
			//$('#order-deleted-tab').addClass('active');
			
			//$('#myTabContent .tab-pane').removeClass('active');
			
			//$('#myTabContent #orderdelete').addClass('active in');
		}
	}).done(function (data) {
		if(data.success){
			$("#order-deleted-content").html(data.orders_html);
			$('.orders_delete_count').html(data.orders_count);
			$('.delete_count').html(data.orders_count);
			$('#orderdelete').show();
        }
	});	  
}

//load default 
function loadOrders(type, url, customerid)
{
        customer = customerid;
	var __url = url;
	if(__url=="")
		__url = jsBaseUrl+"/inprogress/load?type=" + type + "&customerid=" + customerid;
	//alert(__url);
	//
	$.ajax({
		url: __url,
		beforeSend: function() {$('#loading').show(); $("#order-loaded-content-"+type).children().prop('disabled',true);},
		complete: function() {
		//Do whatever you want here              
		$('#loading').hide(); 
		$("#order-loaded-content-"+type).children().prop('disabled',false);	
		//pagination 
		$("#order-loaded-content-"+type+" .pagination a").each(function() {
			$(this).attr('data-href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).on("click touchstart", function(event) { 
				loadOrders("all", $(this).data('href'), customerid);
				event.preventDefault();
				return false;
			});
		});			
		//sorting
		$("#order-loaded-content-"+type+" thead th a").each(function() {
			$(this).attr('data-href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).on("click touchstart", function(event) { 
				loadOrders("all", $(this).data('href'), customerid);
				event.preventDefault();
				return false;
			});			
		}); 		
		//load tooltip
		$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
		//load popover
		$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
		
		$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
		
		$('[id^="item-popover_"]').add('[id^="order-status-popover_"]').on('click', function() {
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
		//popover 
		$('[id^="mail-button-"]').on('click', function() {
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
		
		//desktop version
		/*$('[id^="item-popover_"]').on("mouseenter", function() {
			var e=$(this);
			e.off('hover');
			$.get(e.data('poload'),function(d) {
				//alert(d);
				hideAllPopovers();
				e.popover().popover('show');
			});
		});*/
		//
		/*$('[id^="item-popover_"]').on( "mouseleave", function() {
			hideAllPopovers();
		});
		
		$('[id^="item-popover_"]').focusout(function() {
			hideAllPopovers();
		});	*/	
		/*var openPopup;

		$('[data-toggle="popover"]').on('click',function(){
			if(openPopup){
				$(openPopup).popover('hide');
			}
			openPopup=this;
		});	*/				
		}
	}).done(function (data) {
		//alert(data);
		$("#order-loaded-content-"+type).html(data);
	});	
}

$.get(jsBaseUrl+"/ajaxrequest/loadproblems", function(data){
	//alert(data);
	$('#autocomplete-problem').typeahead({ 
		onSelect: function(item) {
			var problem = item.value;
			var url = jsBaseUrl+"/ajaxrequest/loadtestingdetails?problem=" + problem;
			$.getJSON(url, function(data){
				$('#r_resolution').val(data.s1);
				$('#autocompleteitem').val(data.s2);
				$('#autocompletevalitem').val(data.s3);
			})
		},				
		source:data,
		autoSelect: true,
		items : 10	
	});
},'json');
 
function loadConfirmCleaning(item)
{
	$('#CleaningConfirmation').modal('toggle');
	var formData = {
		itemid : item
	}
	$(document).on("click touchstart", "#confirmButton", function() {
		$.ajax({
			url: jsBaseUrl+"/inprogress/turntocleaning",
			data : formData,
			dataType: 'json',
			success: function(data) {
			}
		}).done(function (data) {
			if(data.success) {
				$('#CleaningConfirmation').modal('hide');
				$('#inprogressdetailsall #cleaning-item-'+item).attr('disabled', true);
				$('#inprogressdetailsall #cleaning-item-'+item).html('Cleaned');
				$('#inprogressdetailsall #cleaning-item-'+item).addClass('cleaned');
				if($('#inprogressdetailsall .cleaningitem').length === $('#inprogressdetailsall .cleaned').length)
					$('#all-cleaned-button').hide();
				//
				if(data.requiretesting)
					$('#inprogressdetailsall #testing-item_'+item).attr('disabled', false);
				
				if(data.readytoship)
					$('#inprogressdetailsall #ready-item-'+item).attr('disabled', false);
				//
				new PNotify({
						title: 'Notifications',
						text: 'Item ' + $('#description-item-'+item).text() + ' {' + $('#serial-item-'+item).text() + '} has been successfully cleaned!',
						type: 'success',
						styling: "bootstrap3",
						opacity: 0.8,
						delay: 5000
					});
			}
		});		
	});
}
//
$(document).on("click", '[id^="ready-item-"]', function() {
	var e = $(this);
	var row = e[0].id.split('-')[2];	
	//alert(row);
    $.ajax({
	 	url: jsBaseUrl+"/inprogress/turnmodelonship",
		beforeSend: function() {$('#loading').show();},
		data: {
			id: row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#loading').hide(); 
			e.removeClass('btn-warning');
			e.addClass('btn btn-success');
			e.html('<span class="glyphicon glyphicon-ok-sign"></span>');
			e.attr('disabled', true);
			$('#ready-to-ship-all').show(); 
			new PNotify({
					title: 'Notifications',
					text: data.html,
					type: 'success',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 5000
				});			
		} else {
			new PNotify({
					title: 'Notifications',
					text: data.html,
					type: 'error',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 5000
				});			
		}
	});
});
//
$(document).on("click", '#RequestReplaceConfirmation #rconfirmButton', function() {
	//alert('hit!');
	var id = $(this).attr('data-id');
	$.ajax({
		url: $(this).attr('data-href'),
		beforeSend: function() {$('#loading').show();},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#RequestReplaceConfirmation').modal('hide');
			$('#loading').hide();
			// 
			new PNotify({
					title: 'Notifications',
					text: data.html,
					type: 'success',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 5000
			});	
			//
			$.ajax({
				url: jsBaseUrl+"/inprogress/loaddetails?id="+id,
				beforeSend: function() {$('#loading').show();},
				dataType: "json"
			}).done(function (data) {
				$('#loading').hide();
				$('#inprogressdetailsall').html(data.html);				
			});
		}
	});
});
//
$(document).on("click", '[id^="request-item_"]', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
	$('#RequestReplaceConfirmation').modal('show');
	$('#rconfirmButton').attr('data-href', jsBaseUrl+'/inprogress/requestreplace?id='+row);
	$('#rconfirmButton').attr('data-id', row);
});
//
$(document).on("click", '[id^="history-item_"]', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
   $.ajax({
		url: jsBaseUrl+"/inprogress/historylog",
		beforeSend: function() {$('#loading').show();},
		data: {
			id: row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#loaded-history-content').html(data.html);
			$('#HistoryLog').modal('show');
			$('#loading').hide();
		}
	});		
});
//
$('#ready-to-ship-all').on("click", function() {
	$('#ReadytoShipAllConfirmation').modal('toggle');
});
//
$('#sconfirmAllButton').on("click", function() {
    $.ajax({
	 	url: jsBaseUrl+"/inprogress/turnonship",
		beforeSend: function() {$('#loading').show();},
		data: {
			id: getUrlParameter('id')
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#loading').hide(); 
			$.each(data.readytoship, function (index, value) {
				var e = $('#inprogressdetailsall #ready-item-'+value);
				e.removeClass('btn-warning');
				e.addClass('btn btn-success');
				e.html('<span class="glyphicon glyphicon-ok-sign"></span>');
				e.attr('disabled', true);
			});	
			//
			new PNotify({
					title: 'Notifications',
					text: data.html,
					type: 'success',
					styling: "bootstrap3",
					opacity: 0.8,
					delay: 5000
				});
			//
			$('#ReadytoShipAllConfirmation').modal('hide');
			$('#ready-to-ship-all').hide();
		}
	});	
});
//
$(document).on('click', '.testing-box', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];
	var _current_background_color = e.css('backgroundColor');
	var nbr_selected_issue = parseInt($('#nbr_issue_selected').text());
	e.css({'backgroundColor': $(this).attr('data-background')});
	if(!e.hasClass('is-selected-issue'))
	{				
		//$('#append_issue_color').append('<span style="padding: 1px 10px 1px 10px;background-color:'+_current_background_color+';" id="bar-color_'+row+'"></span>');
		$('#issue_select_text').show();
		$('#testing-button').show();
		//$('#issue_bar_color_legend').show();
		$('#nbr_issue_selected').html(nbr_selected_issue+1);
		//e.fadeTo( "slow", 0.66 );
		e.addClass('is-selected-issue');
		$('#existing-issue').append('<input type="hidden" class="issue-input" name="issues[]" id="issue-input_'+row+'" value="'+row+'"/>');
	}
	else 
	{
		if(nbr_selected_issue-1==0)
		{
			$('#issue_select_text').hide();
			$('#testing-button').hide();
			//$('#issue_bar_color_legend').hide();
		}
		$('#nbr_issue_selected').html(nbr_selected_issue-1);
		//e.fadeTo( "slow", 1 );
		e.css({'backgroundColor': '#DDD'});
		e.removeClass('is-selected-issue');
		//$('#bar-color_'+row).remove();
		$('#issue-input_'+row).remove();
	}
});

function loadTestingModal(item)
{
   $.ajax({
		url: jsBaseUrl+"/inprogress/testingform",
		data: {
			id: item
		},
		dataType: "json",
		beforeSend: function() {$('#loading').show();},
		complete: function() {
			$.get(jsBaseUrl+"/ajaxrequest/loadmodels", function(data){
				//alert(data);
				$('#autocompleteitem').typeahead({ 
					onSelect: function(item) {
						//---- get model selected id
						$('#autocompletevalitem').val(item.value);
						//----
					},				
					source:data,
					autoSelect: true,
					items : 10	
				});
			},'json');	
			//
			$('#new-testing-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser	
				var $form = $(this);
				$.ajax({
					type: 'POST',  
					beforeSend: function() {$('#loading').show();},
					url: $form.attr('action'),
					data: $form.serialize(),
					dataType: "json",
					encode          : true								 
				}).done(function(data) {
					if(data.success) {
						$('#loading').remove();
						$('.issue-input').hide();
						$('#testingReview').modal('hide');
						//$('#loadedModelForm').html('');		
						$('#inprogressdetailsall #testing-item_'+item).attr('disabled', true);
						$('#inprogressdetailsall #testing-item_'+item).html('Tested');
						$('#inprogressdetailsall #history-item_'+item).attr('disabled', false);
						//$('#inprogressdetailsall #ready-item-'+item).attr('disabled', false);
					}
				});					
			});
			$('#add-issue-form').submit(function(event) {
				event.preventDefault(); // Prevent the form from submitting via the browser	
				var error = 0;
				var $form = $(this);
				//alert($('#r_description').val().length);
				//
				if ($('input[name=problem]').val().length == 0) {
					$('#r_problem-group').addClass('has-error'); // add the error class to show red input
					//$('#r_problem-group').append('<div class="help-block">Problem field is required!</div>'); // add the actual error message under our input
					error++;
				} else 
					$('#r_problem-group').removeClass('has-error');
				if (!$.trim($("#r_resolution").val())) {
					$('#r_description-group').addClass('has-error'); // add the error class to show red input 
					//$('#r_description-group').append('<div class="help-block">Description field is required!</div>'); // add the actual error message under our input
					error++;
				} else 
					$('#r_description-group').removeClass('has-error');
				//
				if(!error) {	
					//alert($form.serialize());
					$.ajax({
						type: 'POST',  
						beforeSend: function() {$('#loading').show();},
						url: $form.attr('action'),
						data: $form.serialize(),
						dataType: "json",
						encode          : true								 
					}).done(function(data) {
						if(data.success) {
							//alert('#testing-item_'+item);
							$form[0].reset();
							$('#load-testing-form-content').html(data.html_testingform);
							$('#loading').hide();
							//$('#testingReview').modal('hide');
							//$('#loadedModelForm').html('');		
							//$('#inprogressdetailsall #testing-item_'+item).attr('disabled', true);
							//$('#inprogressdetailsall #testing-item_'+item).html('Tested');
							//$('#inprogressdetailsall #ready-item-'+item).attr('disabled', false);
						}
					});	
				}
			});
		}
	}).done(function (data) {
		if (data.success) {					    
			$('#loadedModelForm').html(data.html);
			$('#testingReview').modal('toggle');
			$('#loading').hide();
		}
	});		
	/*
	$('#itemId').val(item);
	//
	$('#new-testing-review-form').submit(function(event) {
		var error = 0;
		
		if ($('input[name=problem]').val().length == 0) {
			$('#r_problem-group').addClass('has-error'); // add the error class to show red input
			$('#r_problem-group').append('<div class="help-block">Problem field is required!</div>'); // add the actual error message under our input
			error++;
		} 			
		if ($('#r_description').val().length == 0) {
			$('#r_description-group').addClass('has-error'); // add the error class to show red input
			$('#r_description-group').append('<div class="help-block">Description field is required!</div>'); // add the actual error message under our input
			error++;
		} 				
		if(error) {
			event.preventDefault();
			//
			return false;
		}
	});*/
}
$(function(){
   $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
        $("table").stacktable({
                myClass: 'table table-striped table-bordered'
        });
    }); 
});