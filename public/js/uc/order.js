/***
 * order.js
 * All scripts executed at index
 */
$(document).on("click touchstart", "#optype-group .btn", function(){
	$("#optype-group").button('reset');
});

$(document).on("click touchstart", "#switch-order-quote-type-tab [data-switch-set]", function() {
	$("#switch-order-quote-type-tab button").removeClass('active');
	$(this).addClass('active');
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
$(document).on("click", "#soft_delete_order, #soft_delete_qorder", function(){
    var delHref = $(this).attr('href');
    $('#deleteConfirm').modal('show');
    $('#yes-delete-order').attr('href', delHref);
    return false;
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
var customer = '';

//
function searchOrder(query, url)
{
  $('.mobile-menu').hide();
	var _url;

	if(url != ""){
            _url = url;
        }
        else{
            _url = jsBaseUrl+"/orders/search?query="+query;
        }
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
				//$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('onClick', 'searchOrder("","' + $(this).attr('href') + '");');
				$(this).attr('href', 'javascript:void(0);');
			});
//			$(document).on('click', '#order-search-gridview .pagination a', function() {
//				searchOrder('', $(this).attr('data-href'));
//				return false;
//			});
			//sorting
			$("#order-loaded-content-search thead th a").each(function() {
				$(this).attr('onClick', 'searchOrder("","' + $(this).attr('href') + '");');
				$(this).attr('href', 'javascript:void(0);');
//				$(this).attr('data-href', $(this).attr('href'));
//				$(this).attr('href', '#');
//				$(this).on("click touchstart", function(event) {
//					searchOrder('', $(this).attr('data-href'));
//					event.preventDefault();
//				});
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
			$.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
				$("#order-loaded-content-search").stacktable({
					myClass: 'table table-striped table-bordered'
				});
			});
		}
	});
	//e.preventDefault();
}

function deleteOrders(url, customerid)
{
	var __url = url;
	if(__url=="")
		__url = jsBaseUrl+"/orders/getdeleted?customer=" + customerid;
	$.ajax({
		url: __url,
		beforeSend: function() {$('#loading').show();$('#orderdelete').hide()},
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
			$(".revertOrder, .qrevertOrder").click(function(){
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
			$("#qorder-deleted-content").html(data.qorders_html);
			$('.orders_delete_count').html(data.orders_count);
			$('.qorders_delete_count').html(data.qorders_count);
			$('.delete_count').html(data.orders_count + data.qorders_count);
			//alert(data.orders_count + data.qorders_count);
			$('#orderdelete').show();
			$.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
				$("#order-deleted-content, #qorder-deleted-content").stacktable({
					myClass: 'table table-striped table-bordered'
				});
			});
        }
	});
}

//load default
function loadOrders(type, url, customerid)
{
    customer = customerid;
	var __url = url;
	if(__url=="")
		__url = jsBaseUrl+"/orders/load?type=" + type + "&customerid=" + customerid;
	//alert(__url);
	//
	$.ajax({
		url: __url,
		beforeSend: function() {$('#loading').show(); $("#order-loaded-content-"+type).children().prop('disabled',true);},
		complete: function() {
		//Do whatever you want here
		$('#loading').hide();
		$("#order-main-gridview").children().prop('disabled',false);
		//pagination
		$("#order-main-gridview .pagination a").each(function() {
			$(this).on("click touchstart", function(event) {
				//alert(type);
				loadOrders(type, $(this).attr('href'), customerid);
				event.preventDefault();
				return false;
			});
		});
		//sorting
		$("#order-main-gridview thead th a").each(function() {
            $(this).attr('onClick', 'loadOrders("'+type+'","' + $(this).attr('href') + '", '+customerid+');');
            $(this).attr('href', 'javascript:void(0);');
		});
		//load tooltip
		$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
		//load popover
		$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});

		//$.getScript(jsBaseUrl+"/public/js/functions.js", function( data, textStatus, jqxhr ) {});

		$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});

		$(document).on("click touchstart", "[id^=open-service-modal]", function(){
			var e = $(this);
			var row = e[0].id.split('_')[1];
		   $.ajax({
				url: jsBaseUrl+"/orders/default/relatedservice",
				beforeSend: function(){$('#loading').show();},
				data: {
					id: row
				},
				dataType: "json",
				complete: function() {
					$(document).on("click touchstart", "[id^=return-label-service]", function(){
						var e = $(this);
						var row = e[0].id.split('_')[1];
						OpenReturnLabelModal(row);
					});
				}
			}).done(function (data) {
				$('#loading').hide();
				if (data.success) {
					$('#service_number').html(data.order_number);
					$('#loaded-service-info-content').html(data.html);
					$('#relatedServiceDetails').modal('show');
				}
			});
		});

		$(document).on("click touchstart", "[id^=open-return-label-modal]", function(){
			var e = $(this);
			var row = e[0].id.split('_')[1];
			OpenReturnLabelModal(row);
		});

		$('[id^="order-status-popover_"]').on('click', function() {
			var e=$(this);
			var html = e.data('content').trim();
			//alert(html.length);
			if(html != '')
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
							$('[id^="order-status-popover_"]').popover('destroy');
							e.popover().popover('show');
						}
					}
				});
			}
		});
		$('[id^="item-popover_"]').on('click', function() {
			var e=$(this);
			var html = e.data('content').trim();
			//alert(html.length);
			//if(html != '')
			//{
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
							$('[id^="item-popover_"]').popover('destroy');
							e.popover().popover('show');
						}
					}
				});
			//}
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
		$.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
			$('table').stacktable();
		});
	});
}
//
function loadQuotes(type, url)
{
	var __url = url;
	if(__url=="")
		__url = jsBaseUrl+"/orders/qload?type="+type;
	//
	$.ajax({
		url: __url,
		dataType: "json",
		beforeSend: function() {$('#loading').show(); $("#order-loaded-content-quote").children().prop('disabled',true);},
		complete: function() {
		$("#order-loaded-content-quote").find('script').remove();
		//Do whatever you want here
		$('#loading').hide();
		$("#order-loaded-content-quote").children().prop('disabled',false);
		//pagination
		$("#order-loaded-content-quote .pagination a").each(function() {
			$(this).attr('data-href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).on("click touchstart", function(event) {
				loadQuotes(type, $(this).attr('data-href'));
				event.preventDefault();
				return false;
			});
		});
		//sorting
		$("#order-loaded-content-quote thead th a").each(function() {
			$(this).attr('data-href', $(this).attr('href'));
			$(this).attr('href', '#');
			$(this).on("click touchstart", function(event) {
				loadQuotes(type, $(this).data('href'));
				event.preventDefault();
				return false;
			});
		});
		//load tooltip
		$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
		//load popover
		$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});

		$.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});

		$('[id^="item-popover_"]').on('click', function() {
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
		//alert(data.toSource());
		if (data.success) {
			$("#order-loaded-content-quote").html(data.html);
			$.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
				$("#order-loaded-content-quote").stacktable({
					myClass: 'table table-striped table-bordered'
				});
			});
		}
	});
}
//
$(document).on("click", '.showCustomer', function() {
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

$(document).on("click", '.qoconvert', function(event) {
	var e=$(this);
	$("#quoteConvertConfirm").modal('show');
	$('#yes-convert-quote-order').attr('href', e.attr('href'));
	event.preventDefault();
});
$(document).on('click', function(){
   $('[id^="order-status-popover_"]').popover('destroy');
});
$(function(){
//    var tabWidth = $('#myTab').width();
//    console.log($('#myTab').width());
//    var width = 0;
//    $('#myTab li').each(function(){
//        if($(this).is(":visible")){
//            if($(this).is(':first')){
//                $(this).css({left: 0});
//            } else {
//                var offset = $(this).prev().offset();
//                if(offset.hasOwnProperty("left")){
//                console.log(offset.left);
//                $(this).css({left: (offset.left + 50)});
//                width += parseInt($(this).width());
//                if(width > tabWidth) {
//                    //$(this).hide();
//                }
//            }
//            }
//        }
//    });
//    console.log(width);
});
$(function(){
    $("#myTab").on("touchstart", function(e) {
        var $scroller;
        var $target = $(e.target);

        // Get which element could have scroll bars
        if($target.hasScrollBar()) {
            $scroller = $target;
        } else {
            $scroller = $target
                .parents()
                .filter(function() {
                    return $(this).hasScrollBar();
                })
                .first()
            ;
        }

        // Prevent if nothing is scrollable
        if(!$scroller.length) {
            e.preventDefault();
        } else {
            var top = $scroller[0].scrollTop;
            var totalScroll = $scroller[0].scrollHeight;
            var currentScroll = top + $scroller[0].offsetHeight;

            // If at container edge, add a pixel to prevent outer scrolling
            if(top === 0) {
                $scroller[0].scrollTop = 1;
            } else if(currentScroll === totalScroll) {
                $scroller[0].scrollTop = top - 1;
            }
        }
    });
})
