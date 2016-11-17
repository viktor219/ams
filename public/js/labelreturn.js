function OpenLabelMail(order)
{
	//alert(order);
	var _url;
	_url = jsBaseUrl+"/orders/default/sendlabelmailform";
   $.ajax({
		url: _url,
		data: {
			id: order
		},
		dataType: "json"
	}).done(function (data) {
		if (data.success) {					    
			$('#loaded-content').html(data.html);
			$('#sendLabelEmail').modal('show');
		}
	});
}       