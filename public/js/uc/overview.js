/**
 * Overview script...
 */
$(document).on("click", '#refresh-awaiting-delivery', function() {
	loadAwaitingDeliveryLab();   
});

$(document).on("click", '[id^="delivering-item_"]', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];
	//confirmReadyButton(row);
   $.ajax({
		url: jsBaseUrl+"/site/picklistreadyform",
		data: {
			id: row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#detaisOfPicklistReady').html(data.html);
			if($('#detaisOfPicklistReady').find('#delivery-confirmation-items').length)
				$('#main-delivery-items').show();
			else 
				$('#main-delivery-items').hide();
			$('#picklistReady').modal('show');
		}
	});		
});

$(document).on("click", '[id^="picking-item_"]', function() {
	var e = $(this);
	var row = e[0].id.split('_')[1];	
    $.ajax({
		url: jsBaseUrl+"/site/awaitingdeliverypick",
		beforeSend: function() {$('#loading').show();},
		data: {
			id: row
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#rec-title').html(data.ordername);
			$('#detaisOfQtySerials').html(data.html);
			$('#PickSerials').modal('show');
			$('#loading').hide();
		}
	});	
});

$(document).on("click", '[id^="awaiting-delivery-item_"]', function() {
	var e = this;
	var itemid = e.id.split('_')[1];
	//alert(itemid);
	MarkItemsAsDelivered(itemid);
});	

function confirmSReadyButton(type, item)
{
   $.ajax({
		url: jsBaseUrl+"/site/picklistreadymodel",
		data: {
			type: type,
			itemid: item,
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {	
			$('#btn_'+type+'-'+item).removeClass('btn-warning');
			$('#btn_'+type+'-'+item).addClass('glyphicon glyphicon-ok btn btn-success');
			$('#btn_'+type+'-'+item).removeAttr('onClick');
			$('#btn_'+type+'-'+item).html("");
			$('#picklistReady').modal('hide');
			loadAwaitingDeliveryLab();   
		}
	});
}

function saveSerializedItem(item, model)
{
	$('.col-md-12').removeClass('has-error'); 
	$('.help-block').remove(); // remove the error text
	
	var vcurrentserialgroup = $('#serial-group-'+ item +'');
	var vcurrentserialnumber = $('input[name=serialnumber_'+ item +']');
	var vserialnumber = vcurrentserialnumber.val();
	var vcurrentmodel = model;

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
					"item": item,
					"_csrf":jsCrsf
				};
				//alert(formData.toSource());
				//save serial number...
				$.ajax({
					type        : 'POST',
					url: jsBaseUrl+"/site/saveawaitingserialized",
					data: formData,
					dataType: "json",
					encode          : true
				}).done(function (data) {
					//alert(data.toSource());
					if(data.success)
					{
						ion.sound.play("success");
						//$('#add-requested-service-item-serial')[0].reset();
						$('#serial-group-'+item).removeClass('has-error');
						//change button status 
						//if(data.done){
							//vcurrentserialgroup.hide(); 
							$('[name="serialnumber_'+item+'"]').attr('disabled', true);
							$('#saveSerialBtn_'+item).attr('disabled', true);
						//}
							new PNotify({
									title: 'Notifications',
									text: "Serial " + vserialnumber + "has been added successfully!",
									type: 'success',
									styling: "bootstrap3",
									opacity: 0.8,
									delay: 5000
								});						
					}
				});		
			}
		});	
	}
}

function loadAwaitingDeliveryLab()
{
    $('#loading-awaiting-delivery').show();
	$.ajax({
		xhr: function(){
			var xhr = new window.XMLHttpRequest();
		   //Upload progress
			xhr.upload.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
				   var percentComplete = evt.loaded / evt.total;
				   //Do something with upload progress
//				   alert(percentComplete);
				}
			}, false);
			//Download progress
			xhr.addEventListener("progress", function(evt){
				if (evt.lengthComputable) {
				   var percentComplete = evt.loaded / evt.total;
				   //Do something with download progress
//				   alert(percentComplete);
				}
			}, false);
		   return xhr;
		},
		//beforeSend: function() {$('#loading').show();},
		complete: function () {
			//$.getScript(jsBaseUrl+"/public/js/icheck/icheck.min.js", function( data, textStatus, jqxhr ) {});
			//$('#load-awaiting-delivery-lab-content').iCheck({checkboxClass: 'icheckbox_flat-green',radioClass: 'iradio_flat-green'});
			
			/*$(document).on('ifChecked', '.awaiting_delivery_item', function(event) {
				var e = this;
				var itemid = e.id.split('_')[1];
				MarkItemsAsDelivered(itemid);
			});*/			
			
			//$('.awaiting_delivered_item').iCheck('disable');
			
            $('#loading-awaiting-delivery').hide();
		},
		url: jsBaseUrl + "/site/loadawaitingdeliverylab",
		success: function(data){
			//$('#loading').hide();
			
            $('#load-awaiting-delivery-lab-content').removeClass('min-height-300');
			
			$('#load-awaiting-delivery-lab-content').html(data.html);
		}
	});
}

function loadInventoryStatus(href){
    $('#loaded-overview-inventory-stats #loading').show();
	//alert(e.attr('data-href'));
	$.ajax({
		url: href,
		dataType: 'json',
		success: function(data) {
			if(data.success)
			{
				//alert(data.page);
				//alert(data.nextpage);
				$('#loaded-overview-inventory-stats').html(data.html);
				//e.attr('data-href', jsBaseUrl+"/customers/loadinventorystats?page="+data.nextpage);
                                $('.prev-page-inventory, .next-page-inventory').removeClass('disabled');
                                $('.next-page-inventory').attr('data-href', jsBaseUrl+"/customers/loadinventorystats?page="+data.nextpage);
                                $('.prev-page-inventory').attr('data-href', jsBaseUrl+"/customers/loadinventorystats?page="+(data.prevPage));
                                if(data.nextpage ==2){
                                    $('.prev-page-inventory').addClass('disabled');
                                    $('.next-page-inventory').removeClass('disabled');
                                } else if(!data.hasNext){
                                    $('.next-page-inventory').addClass('disabled');
                                }
			}
		}, complete: function(){
                    $('.inventory-pagination').show();
                    $('#loaded-overview-inventory-stats #loading').hide();
                }
	});
}
function loadShipmentClassment(url){
    $('#loaded-shipments-classments').prev().show();
    $.ajax({
		url: url,
		dataType: 'json',
		success: function(data) {
			if(data.success)
			{
				$('#loaded-shipments-classments').html(data.html);
                if ($(".progress .progress-bar")[0]) {
                    $('.progress .progress-bar').progressbar(); // bootstrap 3
                }                                
				//e.attr('data-href', jsBaseUrl+"/customers/loadshipmentsclassments?page="+data.nextpage);
                                $('.prev-page-shipment, .next-page-shipment').removeClass('disabled');
                                $('.next-page-shipment').attr('data-href', jsBaseUrl+"/customers/loadshipmentsclassments?page="+data.nextpage);
                                $('.prev-page-shipment').attr('data-href', jsBaseUrl+"/customers/loadshipmentsclassments?page="+(data.prevPage));
                                if(data.nextpage == 2){
                                    $('.prev-page-shipment').addClass('disabled');
                                    $('.next-page-shipment').removeClass('disabled');
                                } else if(!data.hasNext){
                                    $('.next-page-shipment').addClass('disabled');
                                }
			}
		},
                complete: function(){
                    $('#loaded-shipments-classments').prev().hide();
                }
	});
}
function getRecentActivity(){
    $.ajax({
        url: jsBaseUrl+'/site/getrecentactivity',
        dataType: 'JSON',
        beforeSend: function(){
            $('#loading-timeline').show();
        },
        success: function(data){
            $('.list-unstyled.timeline').html(data.html);
        },
        complete: function(){
            $('#loading-timeline').hide();
            $('.dashboard-widget-content').removeClass('min-height-300');
        }
    });
}

function getAwaitingdistribution(url){
    if(url==''){
        url = jsBaseUrl+'/site/getawaitingdist';
    }
    $.ajax({
        url: url,
        dataType: 'JSON',
        beforeSend: function(){
            $('#loading-awaiting-dist').show();
        },
        success: function(data){
            $('#awaiting-distribution-content').html(data.html);
            $("#awaiting-distribution .pagination a, #awaiting-distribution th a").each(function () {
                $(this).attr('onClick', 'getAwaitingdistribution("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        },
        complete: function(){
            $('#loading-awaiting-dist').hide();
            $('#awaiting-distribution').removeClass('min-height-300');
        }
    });
}


loadInventoryStatus(jsBaseUrl+'/customers/loadinventorystats?page=1');
getShipRecGraph();
loadShipmentClassment(jsBaseUrl+'/customers/loadshipmentsclassments?page=1');
getRecentActivity();
getAwaitingdistribution('');
$(document).on('change', '.select_department',function(){
        var department_id = $(this).val();
        var modelId = $(this).attr('modelid');
        $.ajax({
            url: jsBaseUrl + "/site/changedepartment",
            beforeSend: function(){
                $('#loading-awaiting-dist').show();
            },
            data: {model: modelId, department: department_id},
            success: function(data){
                getAwaitingdistribution('');
            },
            complete: function(){
                $('#loading-awaiting-dist').hide();
            }
        });
});
//
$(document).on('click', '#inv-stat-load-more-button', function(event) {
	var e = $(this);
        loadInventoryStatus(e.attr('data-href'));
});
//
$(document).on('click', '#shipment-classment-load-more-button', function(event) {
	var e = $(this);
	loadShipmentClassment(e.attr('data-href'));
});
/*function showMoreInvenoryStats()
{
	var e = $('');
	alert(e.data('href'));
		
}*/
//
$(document).ready(function () {
	loadAwaitingDeliveryLab();        
});

function loadAwaitingDistribution(url){
    if(url==''){
        url = jsBaseUrl + "/site/index";
    }
    $.ajax({
        url: url,
        dataType: 'JSON',
        beforeSend: function(){
          $('#loading').show();  
        },
        success: function(data){
            $('#loading').hide();
            $('#awaiting-distribution').html(data.html);
        },
        complete: function(){
            $("#awaiting-distribution .pagination a, #awaiting-distribution th a").each(function () {
                $(this).attr('onClick', 'loadAwaitingDistribution("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        }
    });
}
//
function MarkItemsAsDelivered(itemid)
{ 
	$.ajax({
		url: jsBaseUrl+"/site/turnawaitingstatustoservice",
		data : {itemid: itemid},
		dataType: 'json',
        beforeSend: function(){
			$('#loading').show();  
        },		
		success: function(data) {
			if(data.success)
			{
				loadAwaitingDeliveryLab();
				$('#loading').hide();
			}
		}
	});	

}
function getShipRecGraph(){
    $.ajax({
           url: jsBaseUrl+'/site/getshipments',
           dataType: 'JSON',
           beforeSend: function(){
               $('#loading-canvas').show();
           },
           success: function(data){
               var received = [];
               var shipped = [];
               var max = 1;
               $.each(data.received, function(index, value){
                   var date = new Date(value.created_at).getTime();
                   var comb = [date,value.total];
                   var highest = Math.ceil(value.total/100);
                   if(highest > max){
                      max = highest ;
                   }
                   received.push(comb);
               });
               $.each(data.shipped, function(index, value){
                   var date = new Date(value.created_at).getTime();
                   var comb = [date,value.total];
                   var highest = Math.ceil(value.total/100);
                   if(highest > max){
                      max = highest ;
                   }
                   shipped.push(comb);
               });
               genCanvas(shipped, received, max);
           },
           complete: function(){
               $('#loading-canvas').hide();
           }           
        });
}
function genCanvas(data1, data2, max){
    var maxVal = max * 10;
    var tickSize = (maxVal / 10);
    var ticks = [];
    $.each(data1, function(index, value){
       ticks.push(value[0]);
    });
    $("#canvas_dahs").length && $.plot($("#canvas_dahs"), [
		data1, data2
	], {
		series: {
			lines: {
				show: false,
				fill: true
			},
			splines: {
				show: true,
				tension: 0.01,
				lineWidth: 1,
				fill: 0.4
			},
			points: {
				radius: 0,
				show: true
			},
			shadowSize: 2
		},
		grid: {
			verticalLines: true,
			hoverable: true,
			clickable: true,
			tickColor: "#d5d5d5",
			borderWidth: 1,
			color: '#fff'
		},
		colors: ["rgba(38, 185, 154, 0.38)", "rgba(3, 88, 106, 0.38)"],
		xaxis: {
			tickColor: "rgba(51, 51, 51, 0.06)",
			mode: "time",
                        ticks: ticks,
                        //tickLength: 8,
			tickSize: [7, "day"],
			//tickLength: 10,
			axisLabel: "Date",
			axisLabelUseCanvas: true,
			axisLabelFontSizePixels: 12,
			axisLabelFontFamily: 'Verdana, Arial',
			axisLabelPadding: 10
				//mode: "time", timeformat: "%m/%d/%y", minTickSize: [1, "day"]
		},
		yaxis: {
			//ticks: 8,
                        min: 0,
                        max: maxVal,
                        tickSize: tickSize,
			tickColor: "rgba(51, 51, 51, 0.06)"
		},
		tooltip: false
	});
}