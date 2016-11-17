//store_select2_single
$('#storenumber-group select').removeClass('store_select2_single');
$('#storenumber-group select').hide();
//
//if ($('[name="pushtoggle"]').is(':checked')){ 
$('input[name="returnstore"]').on('switchChange.bootstrapSwitch', function (event, state) {
    //alert(state); // true | false
    if (state) {
        $('#storenumber-group select').addClass('store_select2_single');
        $('#storenumber-group select').select2({width: '100%'});
        //$('#storenumber-group select').show();
    } else {
        $('#storenumber-group select').removeClass('store_select2_single');
        $('#storenumber-group .select2-container').hide();
    }
});


//
var __rloadeditems;
$('#receiving-customer').typeahead({
    onSelect: function (item) {
        $('#customer_Id').val(item.value);
        //
        var items = "";
        customerid = item.value;
        //set default receiving location
        $.get(jsBaseUrl + "/ajaxrequest/getdefaultlocations?customerid=" + customerid, function (data) {
            $('#rselectLocation').select2("val", data.defaultreceivinglocation);
        }, 'json');
        Loadclocations($('#customer_Id').val(), $("#storenumber"), 0, "Returned From");
        $('.palletnumber-group').hide();
        $('.boxnumber-group').hide();
        /*//store number verification
         $.ajax({
         url: jsBaseUrl+"/ajaxrequest/verifycustomerstorenumberstatus?customerid="+customerid,
         dataType: "json"
         }).done(function (data) {
         if (data.success) {
         $('.storenumberinput').show();
         }
         });*/
        //
        $("#autocompleteitem_1").typeahead('destroy');
//		$.get(jsBaseUrl+"/public/autocomplete/json/receiving/"+customerid+"_models.json", function(data){
//			__rloadeditems = data;
        //alert('hit');
        //autocomplete
        $("#autocompleteitem_1").typeahead({
            onSelect: function (item) {
                //---- get model selected id
                var modelid = item.value;
                //alert(modelid);
                $('#autocompletevalitem_1').val(modelid);
                $('#Comment_1').removeAttr('disabled');
                $('#removeAutoCompleteId_1').removeAttr('disabled');
                //pallet count verification
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomerpalletnumberstatus?customerid=" + customerid,
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#uppallet_1').removeAttr('disabled');
                        $('.palletnumber-group').show();
                        $('.r_model-group').removeClass('col-sm-8');
                        $('.r_model-group').addClass('col-sm-4');
                    }
                });
                //box count verification
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomerboxnumberstatus?customerid=" + customerid,
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#upbox_1').removeAttr('disabled');
                        $('.boxnumber-group').show();
                        $('.r_model-group').removeClass('col-sm-8');
                        $('.r_model-group').addClass('col-sm-4');
                    }
                });
                //
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomermodelserialstatus?customerid=" + customerid + "&modelid=" + modelid,
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#entry1 .r_serialnumber').val(1);
                        if ($('#quantity_1').val().length == 0) {
                            $('#entry1 .r_qty-group').addClass('has-error');
                        } else {
                            $('#entry1 .r_qty-group').removeClass('has-error');
                            //$('#Serial_1').enable();
                            // complete wrong code. shouldn't be. below the you will find correct code.
                            $('#Serial_1').removeAttr('disabled');
                            $(document).on('click touchstart', '#Serial_1', function () {
                                openOSerialWindow(customerid, modelid, $('#quantity_1').val(), 1);
                            });
                        }
                    } else if (data.errors)
                        $('#entry1 .r_serialnumber').val(0);
                });
                //----
            },
            matcher: function (item) {
                return true;
            },
            /*sorter: function(items) {
             var beginswith = [],
             caseSensitive = [],
             caseInsensitive = [],
             sortedData = [],
             searchArr = [],
             item;
             while (item = items.shift()) {
             if (!item.name.toLowerCase().indexOf(this.query.toLowerCase()))
             beginswith.push(item);
             else if (~item.name.indexOf(this.query))
             caseSensitive.push(item);
             var returnIndex = exactMatchingResults(item.name, this.query);
             if (returnIndex>=0)
             searchArr.push({sort:returnIndex, text: item.name});
             else
             caseInsensitive.push(item);
             }
             searchArr = searchArr.sort(function(a,b){
             return (a.sort > b.sort) ?-1:0;
             });
             $.each(searchArr, function(key, value){
             sortedData.push(value.text);
             });
             return beginswith.concat(caseSensitive, sortedData, caseInsensitive);
             },
             matcher: function(item) {
             //var searchValue = $('#autocompleteitem_1').val();
             var searchValue = this.query;
             searchValue = searchValue.trim();
             var searchArr = searchValue.split(" ");
             if(multiSearchOr(item, searchArr)){
             return true;
             } else {
             return false;
             }
             },
             highlighter: function(item){
             var searchValue = this.query.trim();
             var searchWords = searchValue.split(" ");
             var length = searchWords.length;
             var highlighString = item;
             for(var i=0;i<length;i++){
             var regex = new RegExp(searchWords[i], 'gi');
             var matcher = highlighString.match(regex , searchWords[i])
             if(matcher != null){
             highlighString = highlighString.replace(regex , "<strong>"+matcher[0]+'</strong>');
             } else {
             highlighString = highlighString.replace(regex , "<strong>"+searchWords[i]+'</strong>');
             }
             }
             return highlighString;
             },*/
            ajax: jsBaseUrl + "/inventory/searchmodels?id=",
//				source:data,
//				autoSelect: true,
            items: 15
        });
        //},'json');
    },
    ajax: jsBaseUrl + "/ajaxrequest/listcountries?query=" + $('#receiving-customer').val(),
    items: 10
});
function multiSearchOr(text, searchWords) {
    var length = searchWords.length;
    var index = -1;
    for (var i = 0; i < length; i++) {
        if (text.toLowerCase().indexOf(searchWords[i].toLowerCase()) >= 0) {
            index = 1;
            break;
        }
    }
    return (index >= 0) ? true : false;
}

function exactMatchingResults(text, searchValue) {
    searchValue = searchValue.trim();
    var searchWords = searchValue.split(" ");
    var length = searchWords.length;
    var index = -1;

    for (var i = 0; i < length; i++) {
        if (text.toLowerCase().indexOf(searchWords[i].toLowerCase()) >= 0) {
            index = (index === -1) ? 0 : index;
            index++;
        }

        var searchString = '\\b' + searchWords[i] + '\\b';
        var regex = new RegExp(searchString);
        var match = text.match(regex);
        if (match != null) {
            index++;
        }
        var searchString = '\\b' + searchWords[i].toLowerCase() + '\\b';
        var regex = new RegExp(searchString, "i");
        var match = text.toLowerCase().match(regex);
        if (match != null) {
            index++;
        }

    }
    return index;
}

$(document).on('click touchstart', '.next_up_pallet_button', function () {
    var e = $(this);
    var id = e.attr('id');
    var row = id.split('_')[1];
    var currentvalue = 1;
    //pallet count verification
    $.ajax({
        url: jsBaseUrl + "/ajaxrequest/verifycustomerpalletnumber?customerid=" + customerid + "&modelid=" + $('#autocompletevalitem_' + row).val(),
        dataType: "json"
    }).done(function (data) {
        if (data.success) {
            //alert(data.value);
            currentvalue = parseInt(data.value) + 1;
            $('#palletnumber_' + row).val(currentvalue);
        } else
            $('#palletnumber_' + row).val(1);

    });
});
//
$(document).on('click touchstart', '.next_up_box_button', function () {
    var e = $(this);
    var id = e.attr('id');
    var row = id.split('_')[1];
    //var currentvalue = parseInt($('#boxnumber_'+row).val());
    var currentvalue = 1;
    $.ajax({
        url: jsBaseUrl + "/ajaxrequest/verifycustomerboxnumber?customerid=" + customerid + "&modelid=" + $('#autocompletevalitem_' + row).val(),
        dataType: "json"
    }).done(function (data) {
        if (data.success) {
            //alert(data.value);
            currentvalue = parseInt(data.value) + 1;
            $('#boxnumber_' + row).val(currentvalue);
        } else
            $('#boxnumber_' + row).val(1);
    });
});
//
$(document).ready(function () {
    //create order page form
    $('form#receive-unscheduled-inventory-form').submit(function (event) {
        $('#r_customer-group').removeClass('has-error');
        $('.r_model-group').removeClass('has-error');
        $('#r_location-group').removeClass('has-error');
        $('.r_qty-group').removeClass('has-error');
        $('#r_serialnumber-group').removeClass('has-error');
        $('.help-block').remove(); // remove the error text
        //
        var vcustomer = $('#receiving-customer').val();
        var vlocation = $('#rselectLocation').val();
        var qtys = $(".rquantity").children();
        var storenumberinputs = $(".storenumberinput").children();
        var palletnumbers = $(".palletnumber").children();
        var boxnumbers = $(".boxnumber").children();
        var items = $(".input_fn").children();
        var vserial = $('input[name=receivingserialnumber]').val();
        var error = 0;
        //
        if (vcustomer.length == 0) {
            $('#r_customer-group').addClass('has-error'); // add the error class to show red input
            error++;
        }
        if (!vlocation) {
            $('#r_location-group').addClass('has-error'); // add the error class to show red input
            error++;
        }
        $(".r_serialnumber").each(function (i) {
            var e = $(this);
            if (e.val() == 1) {
                var row = e[0].id.split('_')[1];
                //alert(row);
                $('#entry' + row + ' .r_model-group').addClass('has-error'); // add the error class to show red input
                $('#entry' + row + ' .r_model-group').append('<div class="help-block">Serial number is required!</div>'); // add the actual error message under our input
                error++;
            }
        });
        //
        $(".rquantity").each(function (i) {
            if (this.value == "") {
                id = this.getAttribute("id");
                $('#' + id).parents("div").eq(0).addClass("has-error"); // add the error class to show red input
                error++;
            }
        });
        //
        $(".input_h").each(function (i) {
            if (this.value == "") {
                id = this.getAttribute("id");
                $('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
                error++;
            }
        });
        //
        /*$(".storenumberinput").each(function(i){
         if(this.value=="") {
         id  = this.getAttribute("id");
         if($('#' + id).is(':visible'))
         $('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
         error++;
         }
         });	*/
        //
        $(".palletnumber").each(function (i) {
            if (this.value == "") {
                id = this.getAttribute("id");
                if ($('#' + id).is(':visible'))
                    $('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
                error++;
            }
        });
        //
        $(".boxnumber").each(function (i) {
            if (this.value == "") {
                id = this.getAttribute("id");
                if ($('#' + id).is(':visible'))
                    $('#' + id).parents("div").eq(1).addClass("has-error"); // add the error class to show red input
                error++;
            }
        });
        //alert(error);
        //
        if (error) {
            event.preventDefault();
            //
            return false;
        }
    });
});
//

$(document).on('click', '.removeAutoCompleteButton', function () {
    //console.log(this.id);
    var buttonId = this.id;
    var buttonNumber = buttonId.replace("removeAutoCompleteId_", "");

    $('#autocompleteitem_' + buttonNumber).val('');
    $('#autocompletevalitem_' + buttonNumber).val('');
    $('#removeAutoCompleteId_' + buttonNumber).attr('disabled', true);
    $('#Serial_' + buttonNumber).attr('disabled', true);
    $('#quantity_'+ buttonNumber).val('');

});


$(function () {

    $('#btnRAdd').click(function () {
        var num = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
            newNum = new Number(num + 1),      // the numeric ID of the new input field being added
            newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
        var prevNum = newNum - 1;
        // manipulate the name/id values of the input inside the new element
        newElem.find('.edit_item_button').attr('id', 'Edit_' + newNum + '');
        newElem.find('.removeAutoCompleteButton').attr('id', 'removeAutoCompleteId_' + newNum + '');
        //newElem.find('.comment_item_button').attr('id', 'Comment_' + newNum + '');
        newElem.find('.clear_item_button').attr('id', 'Clearbtn_' + newNum + '');
        newElem.find('.r_serialnumber').attr('id', 'serialnumber_' + newNum + '').val(0);
        //newElem.find('.storenumberinput').attr('id', 'storenumber_' + newNum + '').val('');
        newElem.find('.next_up_pallet_button').attr('id', 'uppallet_' + newNum + '');
        newElem.find('.next_up_box_button').attr('id', 'upbox_' + newNum + '');
        newElem.find('.itemoption').attr('id', 'itemoption_' + newNum + '').val($('#itemoption_' + prevNum).val()).trigger('change');
        newElem.find('.palletnumber').attr('id', 'palletnumber_' + newNum + '').val(0);
        newElem.find('.boxnumber').attr('id', 'boxnumber_' + newNum + '').val(0);
        newElem.find('.add_serial_button').attr('id', 'Serial_' + newNum + '');
        //newElem.find('.comment').attr('id', 'itemNote_' + newNum + '');
        newElem.find('.palletnumber-group').hide();
        newElem.find('.boxnumber-group').hide();

        newElem.find('#Edit_' + newNum).hide();
        newElem.find('.select2-container').remove();
        /*newElem.find('#itemoption_' + newNum).select2({
         placeholder: "Select An Option",
         width: '100%',
         allowClear: true
         });*/
        newElem.find('#removeAutoCompleteId_' + newNum).attr('disabled', true);
        newElem.find('#Comment_' + newNum).attr('disabled', true);
        newElem.find('#Serial_' + newNum).attr('disabled', true);
        newElem.find('#uppallet_' + newNum).attr('disabled', true);
        newElem.find('#upbox_' + newNum).attr('disabled', true);
        //newElem.find('#itemNote_' + newNum).hide();
        //newElem.find('#configuration_options').attr('id', 'configuration_options' + newNum);
        $('#item-count-order').html('Total cost : $' + checkOrderAmount(false));
        // Title - select
        newElem.find('.rquantity').attr('id', 'quantity_' + newNum).attr('name', 'quantity[]').val('').removeAttr('disabled');

        newElem.find('.clear_serialized-group').hide();

        newElem.find('.qty-group').removeClass("has-success");

        newElem.find('.selectedItems').attr('id', 'item_s-' + newNum).attr('name', 'modelsid[]').val('');

        // First name - text
        newElem.find('.input_fn').attr('id', 'autocompleteitem_' + newNum).attr('name', 'description[]').val('').removeAttr('readonly').removeAttr('disabled');

        newElem.find('.input_h').attr('id', 'autocompletevalitem_' + newNum).val('');

        newElem.find('.qty-group').removeClass('has-error');

        newElem.find('.desc-group').removeClass('has-error');

        newElem.find('.help-block').remove();

        // insert the new element after the last "duplicatable" input field
        $('#entry' + num).after(newElem);
        //$('#ID' + newNum + '_title').focus();

        // enable the "remove" button
        $('#btnRDel').attr('disabled', false);
        //
        $('#Edit_' + newNum).on('click', function (event) {
            var e = $(this);
            var row = e[0].id.split('_')[1];
            //alert(row);
            $('#Edit_' + row).hide();
            $('#entry' + row + ' .input_fn').val('');
            $('#entry' + row + ' .input_fn').removeAttr('readonly');
        });
        //
        var url_models = jsBaseUrl + "/ajaxrequest/loadmodels";
        //$.get(url_models, function(data){
        //jhxhr.done(function(data){
        $('#autocompleteitem_' + newNum).typeahead({
            onSelect: function (item) {
                $('#Comment_' + newNum).removeAttr('disabled');
                //$('#Comment_' + newNum ).show();
                $('#Edit_' + newNum).show();

                console.log(item);

                $('#removeAutoCompleteId_' + newNum).removeAttr('disabled');
                $('#autocompletevalitem_' + newNum).val(item.value);
                //
                //pallet count verification
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomerpalletnumberstatus?customerid=" + $('#customer_Id').val(),
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#uppallet_' + newNum).removeAttr('disabled');
                        newElem.find('.palletnumber-group').show();
                        $('.r_model-group').removeClass('col-sm-8');
                        $('.r_model-group').addClass('col-sm-4');
                    }
                });
                //box count verification
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomerboxnumberstatus?customerid=" + $('#customer_Id').val(),
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#upbox_' + newNum).removeAttr('disabled');
                        newElem.find('.boxnumber-group').show();
                        $('.r_model-group').removeClass('col-sm-8');
                        $('.r_model-group').addClass('col-sm-4');
                    }
                });

                //$('#autocompleteitem_' + newNum).attr('readonly', 'readonly');
                //
                /*$.ajax({
                 url: jsBaseUrl+"/ajaxrequest/verifycustomermodelserialstatus?customerid="+$('#customerId').val()+"&modelid="+item.value,
                 dataType: "json"
                 }).done(function (data) {
                 if (data.success) {
                 $('#entry' + newNum + ' .r_serialnumber-group').html('<input type="text" class="rec_serial" class="form-control" name="receivingserialnumber[' + item.value + '][]" placeholder="Enter Serial Numbers"/>');
                 } else if(data.errors) {
                 $('#entry' + newNum + ' .r_serialnumber-group').html('');
                 }
                 });	*/
                $.ajax({
                    url: jsBaseUrl + "/ajaxrequest/verifycustomermodelserialstatus?customerid=" + $('#customer_Id').val() + "&modelid=" + item.value,
                    dataType: "json"
                }).done(function (data) {
                    if (data.success) {
                        $('#entry' + newNum + ' .r_serialnumber').val(1);
                        if ($('#quantity_' + newNum).val().length == 0) {
                            $('#entry' + newNum + ' .r_qty-group').addClass('has-error');
                        }
                        else {
                            $('#entry' + newNum + ' .r_qty-group').removeClass('has-error');
                            $('#Serial_' + newNum).removeAttr('disabled');
                            $(document).on('click touchstart', '#Serial_' + newNum, function () {
                                openOSerialWindow(customerid, item.value, $('#quantity_' + newNum).val(), newNum);
                            });
                        }
                    } else if (data.errors)
                        $('#entry' + newNum + ' .r_serialnumber').val(0);
                });
            },
            ajax: jsBaseUrl + "/inventory/searchmodels?id=",
//				source:__rloadeditems,
            autoSelect: true,
            items: 10
        });
        //});
        //},'json');
    });

    $('#btnRDel').click(function () {
        // confirmation
        var num = $('.clonedInput').length;
        var current_num = num - 1;
        // how many "duplicatable" input fields we currently have
        $('#entry' + num).slideUp('fast', function () {
            $(this).remove();
            // if only one element remains, disable the "remove" button
            if (current_num === 1)
                $('#btnRDel').attr('disabled', true);
            // enable the "add" button
            $('#btnRAdd').attr('disabled', false).prop('value', "add section");
        });
    });
});
//
$('form#add-receiving-serial-form').submit(function (event) {

    $(this).find('.col-md-12').removeClass('has-error');
    $(this).find('.help-block').remove(); // remove the error text

    var vserialnumber = $('input[name=serialnumber]').val();
    var vcurrentmodel = $('#serialCurrentModel').val();
    var vquantity = $('#serialQuantity').val();
    var vcustomer = $('#customerId').val();
    var vlocation = $('#rselectLocation').val();
    var triggerRow = $('#triggerRow').val();

    if (vserialnumber.length == 0) {
        $('#qserialnumber').focus();
        ion.sound.play("error");
        $('#serial-group').addClass('has-error'); // add the error class to show red input
        $('#serial-group').append('<div class="help-block">Serial Number field is required!</div>'); // add the actual error message under our input
    } else {
        $('#qserialnumber').focus();
        //verify serial numbers...
        $.ajax({
            type: 'POST',
            url: jsBaseUrl + "/receiving/default/saveserial",
            data: {
                "serial": vserialnumber,
                "currentmodel": vcurrentmodel,
                "quantity": vquantity,
                "customerId": vcustomer,
                "location": vlocation,
                "_csrf": jsCrsf
            },
            dataType: "json",
            encode: true
        }).done(function (data) {
            if (data.error) {
                $('#qserialnumber').focus();
                ion.sound.play("error");
                $('#serial-group').addClass('has-error'); // add the error class to show red input
                $('#serial-group').append('<div class="help-block">' + data.html + '</div>'); // add the actual error message under our input
                if (data.code != 500)
                    $('#qserialnumber').val('');
                else {
                    $("#transferConfirm").modal('toggle');
                    $('#confirm-transfer-item').one('click', function (e) {
                        addSerialWithOrNotValidation(vserialnumber, vcurrentmodel, vquantity, vcustomer, vlocation, jsCrsf, true, data.current_quantity, triggerRow);
                        //skip serial require
                        $('#entry' + triggerRow + ' .r_serialnumber').val(0);
                        $('#entry' + triggerRow + ' .r_model-group').removeClass('has-error');
                        $('#entry' + triggerRow + ' .r_model-group').find('.help-block').remove();
                        $('#serial-group').removeClass('has-error');
                        $('#serial-group').find('.help-block').remove();
                        $('#qserialnumber').focus();
                        $('#qserialnumber').val('');
                        $("#transferConfirm").modal('hide');
                        return false;
                        e.preventDefault();
                    });
                }
            } else if (data.success) {
                ion.sound.play("success");
                $('#qserialnumber').focus();
                //skip serial require
                $('#entry' + triggerRow + ' .r_serialnumber').val(0);
                $('#entry' + triggerRow + ' .r_model-group').removeClass('has-error');
                $('#entry' + triggerRow + ' .r_model-group').find('.help-block').remove();
                //clear serial form
                $('#add-receiving-serial-form')[0].reset();
                //change button status
                if (data.done) {
                    $('#addSerials').modal('hide');
                    $('#entry' + triggerRow + ' .rquantity').attr('disabled', true);
                    $('#entry' + triggerRow + ' .input_fn').attr('disabled', true);
                    $('#entry' + triggerRow + ' .add_serial_button').attr('disabled', true);
                    //$('#entry'+triggerRow+' .input_h').val(0);
                    $('#entry' + triggerRow + ' .clear_serialized-group').show();
                } else {
                    loadReceivingSerializedNextModel(vcurrentmodel, vcustomer, vquantity, data.current_quantity, triggerRow);
                }
                //
                if (data.alert_msg) {
                    new PNotify({
                        title: 'Notifications',
                        text: data.alert_msg,
                        type: 'success',
                        styling: "bootstrap3",
                        opacity: 0.8,
                        delay: 5000
                    });
                    $('#qserialnumber').val('');
                }
            }
        });
    }

    // stop the form from submitting the normal way and refreshing the page
    event.preventDefault();
    //
    return false;
});
//
$('#addSerials').on('shown.bs.modal', function () {
    $('#qserialnumber').focus();
});