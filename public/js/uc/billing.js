$(document).on("keyup", '#searchBilling', function (event) {
    var inputContent = $(this).val();
    if (event.keyCode != 46) {
        if ((inputContent.length > 1)) {
            //hide list gridview
            $('#billing-main-gridview').hide();
            //$("#inventory-loaded-content").html('');
            //show search gridview
            $('#billing-search-gridview').show();
            //alert(inputContent);
            //process inventory search 
            searchBilling(inputContent, '');
        }
    }
});
//
$(document).on("keydown", '#searchBilling', function (event) {
    if ((event.keyCode == 13)) {
        //hide list gridview
        $('#billing-main-gridview').hide();

        //show search gridview
        $('#billing-search-gridview').show();
//                $('#myTabContent .tab-pane').removeClass('active');
//                $('#myTabContent .order-all').parent().addClass('active');
        //process order search 
        searchBilling($(this).val(), '');
        //
        event.preventDefault();
        return false;
    }
});


function loadBilling(url, type) {
    var _url = url;
    if (_url == '') {
        _url = jsBaseUrl + "/billing/index";
    }
    $.ajax({
        url: url,
        dataType: "json",
        data: {type: type},
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $("#billing-loaded-content").html(data.html);
            $.getScript(jsBaseUrl + "/public/js/stacktable.js", function (data, textStatus, jqxhr) {
                $("#billing-loaded-content").stacktable({
                    myClass: 'table table-striped table-bordered'
                });
            });
        },
        complete: function () {
            $('#loading').hide();

            $("#billing-loaded-content .pagination a, #billing-loaded-content th a").each(function () {
                $(this).attr('onClick', 'loadBilling("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
//            //load tooltip
//            $.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
//            });
//            //load popover
//            $.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
//            });
//
//            $.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
//            });

            $('[id^="qty-popover_"], [id^="item-popover_"]').click(function () {
                var e = $(this);
                var html = e.data('content');
                //alert(html.length);
                if (!html)
                {
                    $.ajax({
                        url: e.data('poload'),
                        dataType: "json",
                        beforeSend: function () {
                            e.popover().popover('hide');
                            $('#loading').show();
                            e.prop('disabled', true);
                        },
                        success: function (data) {
                            if (data.success)
                            {
                                e.attr('data-content', data.html);
                                $('#loading').hide();
                                e.prop('disabled', false);
                                e.popover().popover('show');
                            }
                        }
                    });
                }
            });
        }
    });
}

function searchBilling(query, url) {
    $('.mobile-menu').hide();
    var _url;
    if (url != "") {
        _url = url;
    }
    else {
        _url = jsBaseUrl + "/billing/search?query=" + query;
    }
    $.ajax({
        url: _url,
        dataType: "json",
        beforeSend: function () {
            $('#loading').show();
            $("#billing-loaded-content-search").children().prop('disabled', true);
        },
        complete: function () {
            $('#loading').hide();
            $("#billing-loaded-content-search").children().prop('disabled', false);
            //pagination 
            $("#billing-loaded-content-search .pagination a").each(function () {
                //$(this).attr('data-href', $(this).attr('href'));
                $(this).attr('onClick', 'searchBilling("","' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //sorting
            $("#billing-loaded-content-search thead th a").each(function () {
                $(this).attr('onClick', 'searchBilling("","' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
//            //load tooltip
//            $.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
//            });
//            //load popover
//            $.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
//            });
//
//            $.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
//            });
            //

            $('[id^="qty-popover_"], [id^="item-popover_"]').click(function () {
                var e = $(this);
                var html = e.data('content');
                //alert(html.length);
                if (!html)
                {
                    $.ajax({
                        url: e.data('poload'),
                        dataType: "json",
                        beforeSend: function () {
                            e.popover().popover('hide');
                            $('#loading').show();
                            e.prop('disabled', true);
                        },
                        success: function (data) {
                            if (data.success)
                            {
                                e.attr('data-content', data.html);
                                $('#loading').hide();
                                e.prop('disabled', false);
                                e.popover().popover('show');
                            }
                        }
                    });
                }
            });
        }
    }).done(function (data) {
        $('#billingsearch').addClass('active');
        //alert(data);
        if (data.success) {
            $("#billing-results-count").html(data.count);
            $("#billing-loaded-content-search").html(data.html);
            $.getScript(jsBaseUrl + "/public/js/stacktable.js", function (data, textStatus, jqxhr) {
                $("#billing-loaded-content-search").stacktable({
                    myClass: 'table table-striped table-bordered'
                });
            });
        }
    });
}

$(function () {
    $('[id^="qty-popover_"], [id^="item-popover_"]').click(function () {
        var e = $(this);
        var html = e.data('content');
        if (!html)
        {
            $.ajax({
                url: e.data('poload'),
                dataType: "json",
                beforeSend: function () {
                    e.popover().popover('hide');
                    $('#loading').show();
                    e.prop('disabled', true);
                },
                success: function (data) {
                    if (data.success)
                    {
                        e.attr('data-content', data.html);
                        $('#loading').hide();
                        e.prop('disabled', false);
                        e.popover().popover('show');
                    }
                }
            });
        }
    });
//    $('.email_invoice').click(function () {
//        $('#sendEmail').modal('show');
//    });
    $(document).on('click', '.move_create_ship', function () {
        $(this).parent().find('.print_pack_button').toggle();
        return false;
    });
    if($('#billing-invoice-gridview').length){
        $.getScript(jsBaseUrl + "/public/js/stacktable.js", function (data, textStatus, jqxhr) {
            $('#billing-invoice-gridview table, #billing-notinvoice-gridview table').stacktable({
                myClass: 'table table-striped table-bordered'
            });
        });
    }
    $(document).on('click', '#previewLabel', function () {
        $('#shippingLabel').modal('show');
    });
    $('.carousel').carousel();
});
function viewBoxConfig(shipmentid, modelid, pallet_box_number) {
    $.ajax({
        url: jsBaseUrl + "/shipping/viewboxdimform",
        beforeSend: function () {
            $('#loading').show();
        },
        data: {
            'shipmentid': shipmentid,
            'modelid': modelid,
            'pallet_box_number': pallet_box_number,
            "_csrf": jsCrsf
        },
        dataType: "json",
        encode: true,
        complete: function () {
            $('#loading').hide();
            /*$('form#box-dimension-form').submit(function(event) {
             event.preventDefault();		
             var form = $(this);			
             //
             });*/
        }
    }).done(function (data) {
        if (data.success) {
            $("#dim_box_model_name").html(data.itemname);
            console.log(data.html);
            $("#load-box-dimension-form-content").html(data.html);
            $('#boxDimension').modal('show');
        }
    });
}