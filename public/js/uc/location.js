$(document).ready(function() {
	// process the form
	//add location
	$('#o-add-location-form ').submit(function(event) {
		$('.col-sm-6').removeClass('has-error'); // remove the error class
		$('.col-sm-3').removeClass('has-error'); // remove the error class
		$('.col-sm-4').removeClass('has-error'); // remove the error class
		$('.help-block').remove(); // remove the error text
		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		//alert($('input[name=laccountnumber]').val());
		
		var shippingmethod = 0;
		if($('#lshippingmethod').val()=="" || !$('#lshippingmethod')[0].value)
			shippingmethod = 0;
		else 
			shippingmethod = $('#lshippingmethod').val();
		//alert(shippingmethod);
		var formData = {
			'customerid' : $('#l_customer').val(),
			'storenum' : $('#storenum').val(),
			'storename' : $('#storename').val(),
			'address' : $('#location_address').val(),
			'address2' : $('#location_secondaddress').val(),
			'country' : $('#location_country').val(),
			'city' : $('#location_city').val(),
			'state' : $('#location_state').val(),
			'zip' : $('#location_zip').val(),
			'email' : $('#location_email').val(),
			'phone' : $('#location_phone').val(),
			'laccountnumber' : $('#laccountnumber').val(),
			'lshippingmethod' : shippingmethod,
			'_csrf' : jsCrsf
		};
		//alert(formData.toSource());
		// process the form
		$.ajax({
			type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url         : jsBaseUrl+"/ajaxrequest/addlocation", // the url where we want to POST
			data        : formData, // our data object
			dataType    : 'json', // what type of data do we expect back from the server
			encode          : true
		})
			// using the done promise callback
		 .done(function(data) {
				if ( ! data.success) {
					// handle errors for email ---------------
					if (data.errors.eemail) {
						
						$('#location_email-group').addClass('has-error'); // add the error class to show red input

						$('#location_email-group').append('<div class="help-block">' + data.errors.eemail + '</div>'); // add the actual error message under our input

					}

					if (data.errors.eaddress) {
						
						$('#location_address-group').addClass('has-error'); // add the error class to show red input

						$('#location_address-group').append('<div class="help-block">' + data.errors.eaddress + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ecountry) {
						
						$('#location_country-group').addClass('has-error'); // add the error class to show red input

						$('#location_country-group').append('<div class="help-block">' + data.errors.ecountry + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ecity) {
						
						$('#location_city-group').addClass('has-error'); // add the error class to show red input

						$('#location_city-group').append('<div class="help-block">' + data.errors.ecity + '</div>'); // add the actual error message under our input

					}

					if (data.errors.estate) {
						
						$('#location_state-group').addClass('has-error'); // add the error class to show red input

						$('#location_state-group').append('<div class="help-block">' + data.errors.estate + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ezip) {
						
						$('#location_zip-group').addClass('has-error'); // add the error class to show red input

						$('#location_zip-group').append('<div class="help-block">' + data.errors.ezip + '</div>'); // add the actual error message under our input

					}																																													
				}
				else
				{
					Loadlocations($('#customer_Id').val(), $("#selectLocation"), data.id);
					Loadlocations($('#customer_Id').val(), $("#selectlocation"), data.id);
					//
					//loadlocationotherdetails($('#shippingcompany'), $('#accountnumber'), $("#shippingmethod"), $("#selectLocation"));
					//load location items for customer
					$('#o-add-location-form ')[0].reset();
					$('#lshippingmethod').select2('val', '');
					$('#location-msg').show();
					$('#location-msg').html(data.message);
					$("#location-msg").delay(1000).fadeOut("slow", function () { 
						$("#location-msg").hide(); 
						$('#addLocation').modal('hide');
						$('#addLocation').hide();
					});
				}
	   });
		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	});		
});		