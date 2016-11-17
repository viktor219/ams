$(".model-loaded-content").each(function() {
	var orderid = $(this).attr('oid');
	var modelid = $(this).attr('mid');
	$.ajax({
		url: jsBaseUrl+"/shipping/loadshippingmodel?orderid=" + orderid + "&modelid=" + modelid,
		dataType: "json",
		complete: function() {
			$.getScript(jsBaseUrl+"/public/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
			$.getScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js", function( data, textStatus, jqxhr ) {});
		}
	}).done(function (data) {
		if (data.success) {
			$('#collapse' + modelid + ' .panel-body').html(data.html);
                        $('#collapse' + modelid + ' .panel-body .grid-view th a, #collapse' + modelid + ' .panel-body .grid-view .pagination a').each(function(){
                            $(this).attr('onClick', 'loadShippingModel("' + $(this).attr('href') + '", '+modelid+', "");');
                            $(this).attr('href', 'javascript:void(0);');
                        });
		}
	});	
});

$(document).on('keydown, keyup', '.pallet_box', function(){
   $(this).val($(this).val().replace(/[^0-9]+/g, ""));
   var inputValue = $(this).val();
   if(inputValue==0){
       inputValue = 1;
   }
   $(this).parents('tr').nextAll().each(function(){
      $(this).find('input').val(inputValue); 
   });
   $(this).parents('tr').find('.ready_button').attr('value', inputValue);
   
});

$(document).on('click', '.ready_button', function(){
   var itemid = $(this).attr('id');
   var type = $(this).attr('type');
   var number = $(this).attr('value');
   var modelid = $(this).parents('.model-loaded-content').attr('mid');
   var oid = $(this).parents('.model-loaded-content').attr('oid');
   if(number=='' || number ==0){
       number = 1;
   }
   $.ajax({
       url: jsBaseUrl+"/shipping/saveboxnumber?itemid=" + itemid + "&type=" + type+'&number='+number,
       beforeSend: function(){
           $('#loading').show();
       },
       success: function(){
           $('#loading').hide();
           loadShippingModel('', modelid, oid);
       }
   });
});

$(document).on('click', '.ready_to_ship', function(){
    var shipmentIds = new Array();
    var inputValues = new Array();
    var type = $(this).parents('tr').find('.ready_button').attr('type');
    $(this).parents('tr').find('.ready_button').each(function(){
        shipmentIds.push($(this).attr('id'));
    });
    $(this).parents('tr').find('.pallet_box').each(function(){
        inputValues.push($(this).val());
    });
    $.ajax({
        url: jsBaseUrl+"/shipping/bulksavebox",
        data: {'itemids': shipmentIds.join(','), numbers: inputValues.join(','), type: type},
        beforeSend: function(){
            $('#loading').show();
        },
        success: function(){
            $('#loading').hide();
        }
    });
    
});

function loadShippingModel(url, modelid, itemid){
    if(url == ''){
        url = jsBaseUrl+"/shipping/loadshippingmodel?orderid=" + itemid + "&modelid=" + modelid;
    }
    $.ajax({
            url: url,
            dataType: "json",
            beforeSend: function(){
              $('#loading').show();
            },
            complete: function() {
                    $('#loading').hide();
                    $.getScript(jsBaseUrl+"/public/js/bootstrap.min.js", function( data, textStatus, jqxhr ) {});
                    $.getScript("http://ajax.aspnetcdn.com/ajax/jquery.validate/1.9/jquery.validate.min.js", function( data, textStatus, jqxhr ) {});
            }
	}).done(function (data) {
            if (data.success) {
                $('#collapse' + modelid + ' .panel-body').html(data.html);
                $('#collapse' + modelid + ' .panel-body .grid-view th a, #collapse' + modelid + ' .panel-body .grid-view .pagination a').each(function(){
                    $(this).attr('onClick', 'loadShippingModel("' + $(this).attr('href') + '", '+modelid+');');
                    $(this).attr('href', 'javascript:void(0);');
                })
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
//
function openBoxConfigModal(ordernumber, modelid, action)
{
	$.ajax({
		url: jsBaseUrl+"/shipping/loadboxdimform",
		data: {
			'orderid': ordernumber,
			'modelid': modelid,
			'action': action,
			"_csrf":jsCrsf 
		},
		dataType: "json",
		encode          : true,
		complete: function() {	
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