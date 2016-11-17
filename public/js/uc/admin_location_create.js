	var url = jsBaseUrl+"/ajaxrequest/listparentlocations"; 
	var items = '';
	$.getJSON(url, function(data){  
		items +='<option value="">Choose a parent location (Optional)</option>';
		$.each(data,function(index,item){ 
			items += data.html; 					
		});
		//
		$('.selectCustomerLocations').html(items);
		var location = $('#storeLocationVal').val();
		$('.selectCustomerLocations').select2({
			placeholder: "Choose a parent location (Optional)",
			width: '100%',
			allowClear: true
		});		
		$('.selectCustomerLocations').select2("val", location);
	}); 