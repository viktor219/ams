/*function ViewPurchasingDetails(id)
 {
 $.ajax({
 url: jsBaseUrl+"/purchasing/view",
 data: {
 id: id
 },
 dataType: "json",
 complete: function() {
 //
 //$('input[name=orderPurchasingId]').val(id);
 $('input[name=_csrf]').val(jsCrsf);
 //
 $('#ReceiveQtyBtn').click(function(e) {
 $("#ReceivePQtyDetails").modal('show');
 });	
 $('#SaveReceiveQtyModal').click(function (event) {
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
 $('#add-receive-qty-form')[0].submit();
 }
 // stop the form from submitting the normal way and refreshing the page
 event.preventDefault();
 //
 return  false;				
 });
 }
 }).done(function (data) {
 if (data.success) {
 $("#title-loaded").html(data.title);
 $("#detaisOfPurchasing").html(data.html);
 $("#purchasingDetails").modal('show');
 }
 });	
 }*/

function deletePurchasing(url) {
    var __url = url;
    if (__url == "")
        __url = jsBaseUrl + "/purchase/getdeleted";
    //alert(__url);
    //
    $.ajax({
        url: __url,
        beforeSend: function () {
            $('#loading').show();
            $('#purchasingdeleted, #purchasinghome').hide()
        },
        dataType: "json",
        complete: function () {
            //$("#order-loaded-content-"+type).find('script').remove();
            //
            var hideAllPopovers = function () {
                $('.popup-marker').each(function () {
                    $(this).popover('destroy');
                });
            };
            //Do whatever you want here              
            $('#loading').hide();
            //pagination 
            $("#purchasing-deleted-content .pagination a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', '#');
                $(this).on("click touchstart", function (event) {
                    deletePurchasing($(this).data('href'));
                    event.preventDefault();
                    return false;
                });
            });
            $('#purchasing-deleted-content [id^="item-popover_"], #items-deleted-content [id^="item-popover_"]').each(function () {
                var e = $(this);
                //
                $.get(e.data('poload'), function (d) {
                    e.attr('data-content', d);
                });
            });
            $('#myTabContent .tab-pane').removeClass('active');
            $('#myTabContent #purchasingdeleted').addClass('active in');
        }
    }).done(function (data) {
        if (data.success) {
            $("#purchasing-deleted-content").html(data.purchase_deleted_html);
            $("#items-deleted-content").html(data.items_deleted_html);
            $(".items_delete_count").html(data.items_delete_count);
            $(".purchase_delete_count").html(data.purchase_delete_count);
            $('.total_delete_count').html(data.items_delete_count + data.purchase_delete_count);
            $(".revertPurchase, .revertItems").click(function () {
                var href = $(this).attr('href');
				var msg;
				if($(this).hasClass('revertPurchase'))
					msg = 'Purchasing Order has been reverted succefully!';
				else if($(this).hasClass('revertItems'))
					msg = 'Items requested has been reverted succefully!';
				//
                $.ajax({
                    url: href,
                    success: function (data) {
                        if (data) {
                            deletePurchasing('');
							new PNotify({
								title: 'Notifications',
								text: msg,
								type: 'info',
								styling: "bootstrap3",
								opacity: 0.8,
								delay: 5000
							});								
                        }
                    }
                });
                return false;
            });
            $(".deleteOrder").click(function () {
                var href = $(this).attr('href');
                $('#deleteConfirm').modal('show');
                $('#yes-delete-order').attr('href', href);
                return false;
            });
            //sorting
            $("#purchasing-deleted-content .pagination a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', '#');
                $(this).on("click touchstart", function (event) {
                    deletePurchasing($(this).data('href'));
                    event.preventDefault();
                    return false;
                });
            });
            $('#purchasingdeleted').show();
        }
    });
}

function loadPurchasing(url) {
    var __url = url;
    if (__url == "")
        __url = jsBaseUrl + "/purchasing/load";
    //alert(__url);
    //
    $.ajax({
        url: __url,
        beforeSend: function () {
            $('#loading').show();
            $('#purchasinghome').hide()
        },
        dataType: "json",
        complete: function () {
            //$("#order-loaded-content-"+type).find('script').remove();
            //Do whatever you want here              
            $('#loading').hide();
            //pagination 
            $('#purchasing-main-gridview [id^="item-popover_"]').each(function () {
                var e = $(this);
                //
                $.get(e.data('poload'), function (d) {
                    e.attr('data-content', d);
                });
            });
            $('#myTabContent .tab-pane').removeClass('active');
            $('#myTabContent #purchasinghome, #incomingpurchasingall').addClass('active in');
        }
    }).done(function (data) {
        if (data.success) {
            $("#items-load-content").html(data.items_html);
            $("#incomingpurchasingall").html(data.active_purchasehtml);
            $("#incomingpurchasingexhausted").html(data.inactive_purchasehtml);
            $(".revertPurchase, .revertItems").click(function () {
                var href = $(this).attr('href');
                $.ajax({
                    url: href,
                    success: function (data) {
                        if (data) {
                            deletePurchasing('');
                        }
                    }
                });
                return false;
            });
            $('#purchasing-main-gridview .pagination a, #incomingpurchasingall .pagination a, #incomingpurchasingexhausted .pagination a').click(function () {
                loadPurchasing($(this).attr('data-href'));
                return false;
            });
            //sorting
            $("#items-load-content .grid-view th a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', '#');
                $(this).on("click touchstart", function (event) {
                    loadPurchasing($(this).data('href'));
                    event.preventDefault();
                    return false;
                });
            });
            $("#soft_delete_purchase_item, #soft_delete_item").click(function () {
                var delHref = $(this).attr('href');
                $('#deleteConfirm').modal('show');
                $('#yes-delete-order').attr('href', delHref);
                return false;
            });
//            $('#purchasinghome').show();
            $('#purchasinghome').css({'display': ''});
        }
    });
}


function ReceivePurchaseOrder(id)
{
    $.ajax({
        url: jsBaseUrl + "/purchasing/receive",
        data: {
            id: id
        },
        dataType: "json"
    }).done(function (data) {
        if (data.success) {
            $("#number_generated").html(data.title);
            $("#ReceivePQtyDetails").modal('show');
            $('#SaveReceiveQtyModal').click(function (event) {
                window.location = jsBaseUrl + "/purchasing/savereceiveqty?id=" + data.id;
                // stop the form from submitting the normal way and refreshing the page
                event.preventDefault();
                //
                return  false;
            });
        }
    });
}
$(document).on("click", "#soft_delete_purchase_item, #soft_delete_item", function () {
    var delHref = $(this).attr('href');
    $('#deleteConfirm').modal('show');
    $('#yes-delete-order').attr('href', delHref);
    return false;
});
function openVendorModal()
{
    window.open(jsBaseUrl + "/vendor/create", '_blank');
}

$('[id^="item-popover_"]').each(function () {
    var e = $(this);
    //
    $.get(e.data('poload'), function (d) {
        e.attr('data-content', d);
    });
});

$(document).on("input", "#detaisOfPurchasingReceive input[type='number']", function () {
    this.value = this.value.replace(/[^0-9\.]/g, '');
});
//
$(document).on("keyup", "#detaisOfPurchasingReceive input[type='number']", function () {
    var max = parseInt($(this).attr('max'));
    var quantity_entered = parseInt($(this).val());
    if (quantity_entered > max)
        $(this).parents("div").eq(0).addClass('has-error');
    else
        $(this).parents("div").eq(0).removeClass('has-error');
});
//
$(document).on("click touchstart", '#searchPurchasingBtn', function (event) {
    if ($('#searchPurchasing').val().length === 0)
        alert('Search field value missing!');
    else {
        //show search gridview
        $('#myTab li').hide();
        $('#purchasing-search-tab').show();
        $('[role="presentation"]').removeClass('active');
        $('[role="tabpanel"]').removeClass('active');
        $('#purchasing-search-tab').removeClass('active');
        $('#purchasingsearch').removeClass('active');
        $('#purchasing-search-tab').addClass('active');
        $('#purchasingsearch').addClass('fade active in');
        //process order search 
        searchPurchasing($('#searchPurchasing').val(), '');
    }
});
//
$(document).on("keyup", '#searchPurchasing', function (event) {
    var inputContent = $(this).val();
    if (event.keyCode != 46) {
        if ((inputContent.length > 1)) {
            $('#myTab li').hide();
            //show search gridview
            $('#purchasing-search-tab').show();
            $('[role="presentation"]').removeClass('active');
            $('[role="tabpanel"]').removeClass('active');
            $('#purchasing-search-tab').removeClass('active');
            $('#purchasingsearch').removeClass('active');
            $('#purchasing-search-tab').addClass('active');
            $('#purchasingsearch').addClass('fade active in');
            //process inventory search 
            searchPurchasing(inputContent, '');
        }
    }
});
//
$(document).on("keydown", '#searchPurchasing', function (event) {
    if ((event.keyCode == 13)) {
        //show search gridview
        $('#myTab li').hide();
        $('#purchasing-search-tab').show();
        $('[role="presentation"]').removeClass('active');
        $('[role="tabpanel"]').removeClass('active');
        $('#purchasing-search-tab').removeClass('active');
        $('#purchasingsearch').removeClass('active');
        $('#purchasing-search-tab').addClass('active');
        $('#purchasingsearch').addClass('fade active in');
        //process order search 
        searchPurchasing($(this).val(), '');
        //
        event.preventDefault();
        return false;
    }
});
//
function searchPurchasing(query, url)
{
    var _url;
    if (query == ""  && url!="")
        _url = url;
    else
        _url = jsBaseUrl + "/purchasing/search?query=" + query;
    $.ajax({
        url: _url,
        dataType: "json",
        beforeSend: function () {
            $('#loading').show();
//            $('#loading-search').show();
            $("#purchasing-loaded-content-search").children().prop('disabled', true);
        },
        complete: function () {
//            $('#loading-search').hide();
            $('#loading').hide();
            $("#purchasing-loaded-content-search").children().prop('disabled', false);
            //
            var hideAllPopovers = function () {
                $('.popup-marker').each(function () {
                    $(this).popover('destroy');
                });
            };
            //pagination 
            $("#purchasing-loaded-content-search .pagination a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', '#');
            });
            $(document).on('click', '#purchasing-main-gridview .pagination a', function () {
                searchPurchasing('', $(this).attr('data-href'));
                return false;
            });
            //sorting
            $("#purchasing-loaded-content-search thead th a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', '#');
                $(this).on("click touchstart", function (event) {
                    searchPurchasing('', $(this).attr('data-href'));
                    event.preventDefault();
                });
            });
            //load tooltip
            $.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
            });
            //load popover
            $.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
            });

            $.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
            });

            $('[id^="item-popover_"]').each(function () {
                var e = $(this);
                //
                $.get(e.data('poload'), function (d) {
                    e.attr('data-content', d);
                });
            });
            $(".deleteOrder").click(function () {
                var href = $(this).attr('href');
                $('#deleteConfirm').modal('show');
                $('#yes-delete-order').attr('href', href);
                return false;
            });
        }
    }).done(function (data) {
        //alert(data);
        if (data.success) {
            $("#order-results-count").html(data.count);
            $("#purchasing-loaded-content-search").html(data.html);
        }
    });
    //e.preventDefault();
}