function OpenLabelMail(order)
{
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
			$('#sendLabelEmail #loaded-content').html(data.html);
			$('#sendLabelEmail').modal('show');
		}
	});	
}
//
$('form#label-send-mail-form').submit(function(event) {
	event.preventDefault(); // Prevent the form from submitting via the browser
	var form = $(this);
	$.ajax({
		type: 'POST',  
		url: jsBaseUrl+"/orders/sendreturnedlabelmail",
		data: form.serialize(),
		dataType: "json",
		encode : true								 
	}).done(function(data) {
		if(data.success)
		{
			$('#to-mail-group').removeClass('has-error');
			form[0].reset();						
			new PNotify({
				title: 'Notifications',
				text: 'Label has been successfully sent!',
				type: 'success',
				styling: "bootstrap3",
				opacity: 0.8,
				delay: 5000
			});		
		}
		else 
		{
			$('#to-mail-group').addClass('has-error');
			new PNotify({
				title: 'Notifications',
				text: data.message,
				type: 'error',
				styling: "bootstrap3",
				opacity: 0.8,
				delay: 5000
			});				
		}
	});							
});