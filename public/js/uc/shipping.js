$(document).on('keydown, keyup', '.pallet_box', function () {
    $(this).val($(this).val().replace(/[^0-9]+/g, ""));
    var inputValue = $(this).val();
    if (inputValue == 0) {
        inputValue = 1;
    }
    $(this).parents('tr').nextAll().each(function () {
        $(this).find('input').val(inputValue);
    });
    $(this).parents('tr').find('.ready_button').attr('value', inputValue);

});
//$(document).on('click' ,'.print_pack_label', function(){
//    $(this).parent().toggleClass('open');
//});
$(function () {
    $('#printPackingLabel').on('change', function () {
        if($(this).val()){
            var shipmentId = $(this).val();
            var orderId = $(this).attr('order');
            window.open(jsBaseUrl + '/shipping/printpackinglist?id=' + orderId + '&shipment=' + shipmentId);
        }
    });

    $(document).on('click', '.move_create_ship', function () {
        $(this).parent().find('.print_pack_button').toggle();
        return false;
    });

    $(document).on('click', '#previewLabel', function () {
        $('#shippingLabel').modal('show');
    });
    $('.carousel').carousel();

    $(document).on('click', '.select_boxpallet', function () {
        var url = $(this).attr('href');
        $.ajax({
            url: url,
            beforeSend: function () {
                $('#loading').show();
            },
            complete: function () {
                $('#loading').hide();
                location.reload();
            }
        });
        return false;
    });

    $(document).on('click', '.ready_to_shipment', function () {
        var href = $(this).attr('href');
        var thisObj = $(this);
        thisObj.attr('href', 'javascript:void(0);')
        $.ajax({
            url: href,
            dataType: 'JSON',
            beforeSend: function () {
                $('#loading').show();
            },
            success: function(data){
                $('#createShipment').removeAttr('disabled');
                new PNotify({
                    title: 'Notifications',
                    text: data.message,
                    type: (data.success) ? 'success': 'error',
                    styling: "bootstrap3",
                    opacity: 0.8,
                    delay: 5000
		});  
            },
            complete: function () {
                $('#loading').hide();
                thisObj.toggleClass('ready_to_shipment ready_to_ship').removeClass('btn-info').addClass('btn-success');
                thisObj.parents('td').find('.set_shipready').each(function () {
                    $(this).removeClass('btn-info set_shipready').addClass('btn-success ready_button').attr('href', 'javascript:void(0);');
                });
            }
        });
        return false;
    });
    
    $(document).on('click', '#save_box_dimensions', function () {
        var error = false;
        var errorMessage = '';
        $('#box-dimension-form input').each(function(){
            if(!$(this).val()){
                var name = $(this).attr('name');
                name = name.charAt(0).toUpperCase() + name.slice(1);
                errorMessage += name + ' is required.<br/>';               
                error = true;
            }
        });
        if(!error){
            $.ajax({
                url: $('#box-dimension-form').attr('action'),
                data: $('#box-dimension-form').serialize(),
                beforeSend: function () {
                    $('#loading').show();
                },
                complete: function () {
                    $('#loading').hide();
                    var shipmentId = $('#box-dimension-form input[name="shipmentId"]').val();
                    var modelId = $('#box-dimension-form input[name="modelId"]').val();
                    var pallet_box_number = $('#box-dimension-form input[name="pallet_box_number"]').val();
                    $('.weight_box_'+shipmentId+'_'+modelId+'_'+pallet_box_number).removeClass('btn-info').addClass('btn-success');
                    $('.weight_box_'+shipmentId+'_'+modelId+'_'+pallet_box_number + ' span').removeClass('glyphicon-plus').addClass('glyphicon-edit');
                    $('#boxDimension').modal('hide');
                    new PNotify({
                        title: 'Notifications',
                        text: '<b>Success!</b> Weight & Dimensions has been successfully saved!',
                        type: 'success',
                        styling: "bootstrap3",
                        opacity: 0.8,
                        delay: 5000
                    });
                }
            });
        } else {
            new PNotify({
                    title: 'Notifications',
                    text: errorMessage,
                    type: 'error',
                    styling: "bootstrap3",
                    opacity: 0.8,
                    delay: 5000
                }); 
        }
        return false;
    });
    
    $(".model-loaded-content").each(function () {
    var orderid = $(this).attr('oid');
    var modelid = $(this).attr('mid');
    $.ajax({
        url: jsBaseUrl + "/shipping/loadshippingmodel?orderid=" + orderid + "&modelid=" + modelid,
        dataType: "json",
        beforeSend: function(){
           $('#loading').show(); 
        },
        complete: function () {
            $('#loading').hide();
//            $.getScript(jsBaseUrl + "/public/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
//            });
//            $.getScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js", function (data, textStatus, jqxhr) {
//            });
        }
    }).done(function (data) {
        if (data.success) {
            $('#collapse' + modelid + ' .panel-body').html(data.html);
            $('#collapse' + modelid + ' .panel-body .grid-view th a, #collapse' + modelid + ' .panel-body .grid-view .pagination a').each(function () {
                $(this).attr('onClick', 'loadShippingModel("' + $(this).attr('href') + '", ' + modelid + ', "");');
                $(this).attr('href', 'javascript:void(0);');
            });
            $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                $('#collapse' + modelid + ' .panel-body table').stacktable({
                        myClass: 'table table-striped table-bordered'
                });
            });            
        }
    });
});
    $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
        $('#myOrderDet').stacktable();
    });  
});
$(document).on('click', '.ready_button', function () {
    var itemid = $(this).attr('id');
    var type = $(this).attr('type');
    var number = $(this).attr('value');
    var modelid = $(this).parents('.model-loaded-content').attr('mid');
    var oid = $(this).parents('.model-loaded-content').attr('oid');
    if (number == '' || number == 0) {
        number = 1;
    }
    $.ajax({
        url: jsBaseUrl + "/shipping/saveboxnumber?itemid=" + itemid + "&type=" + type + '&number=' + number,
        dataType: 'JSON',
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $('#loading').hide();
//            loadShippingModel('', modelid, oid);
            new PNotify({
                title: 'Notifications',
                text: data.message,
                type: (data.success) ? 'success': 'error',
                styling: "bootstrap3",
                opacity: 0.8,
                delay: 5000
            });
        }
    });
    return false;
});

$(document).on('click', '.set_shipready', function(){
    var thisObj = $(this);
    var href = $(this).attr('href');
    $.ajax({
        url: href,
        dataType: 'JSON',
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $('#loading').hide();
            thisObj.removeClass('set_shipready btn-info').addClass('ready_button btn-success').attr('href', 'javascript:void(0);');
            var model_loaded = thisObj.parents('.model-loaded-content');
            if(!thisObj.parent().parent().parent().parent().find('.set_shipready').length){
                model_loaded.parents('tr').find('.ready_to_shipment').addClass('ready_to_ship btn-success').removeClass('ready_to_shipment btn-info').attr('href', 'javascript:void(0);');
                $('#createShipment').removeAttr('disabled');
            }
           $('#createShipment').removeAttr('disabled'); 
                new PNotify({
                    title: 'Notifications',
                    text: data.message,
                    type: (data.success) ? 'success': 'error',
                    styling: "bootstrap3",
                    opacity: 0.8,
                    delay: 5000
                });
        }
    });
    return false;
});

$(document).on('click', '.ready_to_ship', function () {
    var shipmentIds = new Array();
    var inputValues = new Array();
    var type = $(this).parents('tr').find('.ready_button').attr('type');
    $(this).parents('tr').find('.ready_button').each(function () {
        shipmentIds.push($(this).attr('id'));
    });
    $(this).parents('tr').find('.pallet_box').each(function () {
        inputValues.push($(this).val());
    });
    $.ajax({
        url: jsBaseUrl + "/shipping/bulksavebox",        
        data: {'itemids': shipmentIds.join(','), numbers: inputValues.join(','), type: type},
        dataType: 'JSON',
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            $('#loading').hide();
            if(data.message){
                new PNotify({
                        title: 'Notifications',
                        text: data.message,
                        type: (data.success) ? 'success': 'error',
                        styling: "bootstrap3",
                        opacity: 0.8,
                        delay: 5000
                });
            }
        }
    });

});

function loadShippingModel(url, modelid, itemid) {
    if (url == '') {
        url = jsBaseUrl + "/shipping/loadshippingmodel?orderid=" + itemid + "&modelid=" + modelid;
    }
    $.ajax({
        url: url,
        dataType: "json",
        beforeSend: function () {
            $('#loading').show();
        },
        complete: function () {
            $('#loading').hide();
            $.getScript(jsBaseUrl + "/public/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
            });
            $.getScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js", function (data, textStatus, jqxhr) {
            });
        }
    }).done(function (data) {
        if (data.success) {
            $('#collapse' + modelid + ' .panel-body').html(data.html);
            $('#collapse' + modelid + ' .panel-body .grid-view th a, #collapse' + modelid + ' .panel-body .grid-view .pagination a').each(function () {
                $(this).attr('onClick', 'loadShippingModel("' + $(this).attr('href') + '", ' + modelid + ');');
                $(this).attr('href', 'javascript:void(0);');
            })
            $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                $('#collapse' + modelid + ' .panel-body table').stacktable({
                        myClass: 'table table-striped table-bordered'
                });
            });                     
        }
    });
}
//
$('form#box-dimension-form').validate({
    rules: {
        weight: {
            required: true,
        },
        height: {
            required: true,
        },
        length: {
            required: true,
        },
        depth: {
            required: true,
        },
    },
    messages: {
        weight: "Weight is required.",
        height: "Height is required.",
        length: "Length is required.",
        depth: "Depth is required.",
    },
    submitHandler: function (form) {
        //$('form#box-dimension-form').attr('action', jsBaseUrl+"/shipping/saveboxdimension?order="+$('form#box-dimension-form').find('#orderId').val()+"&model="+$('form#box-dimension-form').find('#modelId').val()); 
        form.submit();
        /*$.ajax({
         type: 'POST',  
         url: jsBaseUrl+"/shipping/saveboxdimension",
         data: $('form#box-dimension-form').serialize(),
         dataType: "json",
         encode          : true								 
         }).done(function(data) {
         $('form#box-dimension-form')[0].reset();
         $('#box-dimension-msg').html("Weight & Dimensions has been successfully saved!");
         $('#box-dimension-msg').show();							
         $("#box-dimension-msg").delay(2000).fadeOut("slow", function () { 
         $("#box-dimension-msg").hide(); 
         $('#boxDimension').modal('hide');
         });								
         });*/
    }
});
$(document).on('click', '#create_shipment', function(){
    var url = $(this).attr('validate-url');
   $.ajax({
        url: url,
        beforeSend: function () {
            $('#loading').show();
        },        
        dataType: "json",
        encode: true,
        complete: function () {
            $('#loading').hide();
        }
    }).done(function (data) {
        if (data.hasError) {
            $('#validateModal .modal-body .error').html(data.html);
            $('#validateModal').modal('show');
        } else {
            $('#create-ship-form').submit();
        }
    });
    return false;
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
            $("#load-box-dimension-form-content").html(data.html);
            $('#boxDimension').modal('show');
        }
    });
}

function openBoxConfigModal(shipmentid, modelid, pallet_box_number)
{
    $.ajax({
        url: jsBaseUrl + "/shipping/loadboxdimform",
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
            $("#load-box-dimension-form-content").html(data.html);
            $('#boxDimension').modal('show');
        }
    });
}

$(document).ready(function(){
    function searchInShipping(ordernumber) {
        $('.mobile-menu').hide();
        var _url = jsBaseUrl + "/shipping/searchinshipping?ordernumber=" + ordernumber;
        $.ajax({
            url: _url,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() { $('#loading').show();},
            complete: function(data) {
                $('#loading').hide();
            },
            error: function() {
                
            }
        }).done(function(data) {
            $('#loading').hide();
            $('#in-shipping-search-panel').html(data.html);
        });
    }

    $('#search-in-shipping').on('search keyup', function(event){
        searchInShipping($(this).val());
    });

    $('#search-btn-in-shipping').click(function(event){
        searchInShipping($('#search-in-shipping').val());
    });

    function searchReadyToShip(ordernumber) {
        $('.mobile-menu').hide();
        var _url = jsBaseUrl + "/shipping/searchreadytoship?ordernumber=" + ordernumber;
        $.ajax({
            url: _url,
            type: 'GET',
            dataType: 'json',
            beforeSend: function() { $('#loading').show();},
            complete: function(data) {
                $('#loading').hide();
            },
            error: function() {
                
            }
        }).done(function(data) {
            $('#loading').hide();
            $('#seach-ready-to-ship-panel').html(data.html);
        });
    }

    $('#search-ready-to-ship').on('search keyup', function(event){
        searchReadyToShip($(this).val());
    });

    $('#search-btn-ready-to-ship').click(function(event){
        searchInShipping($('#search-ready-to-ship').val());
    });

    searchInShipping("");
    searchReadyToShip("");
})
