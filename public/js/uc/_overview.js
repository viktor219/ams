/**
 * Overview script...
 */
 
function loadAwaitingDeliveryLab()
{
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
		complete: function () {
			//$.getScript(jsBaseUrl+"/public/js/icheck/icheck.min.js", function( data, textStatus, jqxhr ) {});
			$('#load-awaiting-delivery-lab-content').iCheck({checkboxClass: 'icheckbox_flat-green',radioClass: 'iradio_flat-green'});
			$(document).on('ifChecked', '.awaiting_delivery_item', function(event) {
				var e = this;
				var itemid = e.id.split('_')[1];
				MarkItemsAsDelivered(itemid);
			});			
			$('.awaiting_delivered_item').iCheck('disable');
		},
		url: jsBaseUrl + "/site/loadawaitingdeliverylab",
		success: function(data){
			$('#load-awaiting-delivery-lab-content').html(data.html);
		}
	});
}
//
$(document).on('click', '#inv-stat-load-more-button', function(event) {
	var e = $(this);
	//alert(e.attr('data-href'));
	$('#loading').show();
	$.ajax({
		url: e.attr('data-href'),
		dataType: 'json',
		success: function(data) {
			if(data.success)
			{
				//alert(data.page);
				//alert(data.nextpage);
				$('#loaded-overview-inventory-stats').html(data.html);
				e.attr('data-href', jsBaseUrl+"/customers/loadinventorystats?page="+data.nextpage);
			}
		}, complete: function(){
             $('#loading').hide();
        }
	});
});
//
$(document).on('click', '#shipment-classment-load-more-button', function(event) {
	var e = $(this);
	$.ajax({
		url: e.attr('data-href'),
		dataType: 'json',
		success: function(data) {
			if(data.success)
			{
				$('#loaded-shipments-classments').html(data.html);
                if ($(".progress .progress-bar")[0]) {
                    $('.progress .progress-bar').progressbar(); // bootstrap 3
                }                                
				e.attr('data-href', jsBaseUrl+"/customers/loadshipmentsclassments?page="+data.nextpage);
			}
		}
	});
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
//
function MarkItemsAsDelivered(itemid)
{
	$.ajax({
		url: jsBaseUrl+"/site/turnawaitingstatustoservice",
		data : {itemid: itemid},
		dataType: 'json',
		success: function(data) {
			if(data.success)
			{
				loadAwaitingDeliveryLab();
			}
		}
	});	
}