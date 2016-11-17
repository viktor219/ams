$(document).ready(function() {
	// process the form
	//add location
	$('#request-item-form').submit(function(event) {
		$('#r_description-group').removeClass('has-error'); // remove the error class
		$('#r_manpart-group').removeClass('has-error'); // remove the error class
		$('.help-block').remove(); // remove the error text
		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		var formData = {
			'rdescription' : $('#r_description').val(),
			'rmanpartnum' : $('#r_manpart').val(),
			'rcustomer' : $('#selectCustomer').val(),
			'_csrf' : jsCrsf
		};
		// process the form
		$.ajax({
			type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url         : jsBaseUrl+"/ajaxrequest/addnewitemrequest", // the url where we want to POST
			data        : formData, // our data object
			dataType    : 'json', // what type of data do we expect back from the server
			encode          : true
		})
			// using the done promise callback
		 .done(function(data) {
				if ( ! data.success) {
					// handle errors for email ---------------
					if (data.errors.edescription) {
						
						$('#r_description-group').addClass('has-error'); // add the error class to show red input

						$('#r_description-group').append('<div class="help-block">' + data.errors.edescription + '</div>'); // add the actual error message under our input

					}

					if (data.errors.emanpart) {
						
						$('#r_manpart-group').addClass('has-error'); // add the error class to show red input

						$('#r_manpart-group').append('<div class="help-block">' + data.errors.emanpart + '</div>'); // add the actual error message under our input

					}																																												
				}
				else
				{
					$('#request-item-form')[0].reset();
					$('#itemrequest-msg').show();
					$('#itemrequest-msg').html(data.message);
					$("#itemrequest-msg").delay(2000).fadeOut("slow", function () { 
						$("#itemrequest-msg").hide(); 
						$('#requestItem').modal('hide');
					});
					window.location.reload();
				}
	   });
		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	});		
});		