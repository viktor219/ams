$(document).ready(function() {
    //Loadlocations("", $("#selectShippinglocation"), 0);
	//
	$(document).on("click touchstart", "#add-shipping-location", function() {
		$('#addLocation').modal('toggle');
	});
	$(document).on("click touchstart", "#switch-shipping-detail-tab [data-switch-set]", function() {
		$("#switch-shipping-detail-tab button").removeClass('btn-primary active');
		$("#switch-shipping-detail-tab button").addClass('btn-dark');
		$(this).removeClass('btn-dark');
		$(this).addClass('btn-primary active');
		var value = parseInt($(this).data("switch-value"));
		$("#defaultshippingchoice").val(value).trigger('change');
		//clear old entries
		/*$('#defaultaccountnumber').val("");
		$('#c1_shippingcompany').prop('selectedIndex', 0);
		$("#defaultshippingmethod").select2("val", "");		*/				
		//alert(value);
		/*if(value==0) //Load asset settings 
		{
			var url = jsBaseUrl+"/ajaxrequest/getshippingassetdetails";
			$.getJSON(url, function(data){
				$('#defaultaccountnumber').val(data.s1);
				$('#c1_shippingcompany').val(data.s2);
				if($("#c1_shippingcompany").val().length > 0)
					loadShippingMethods($("#c1_shippingcompany"), $("#defaultshippingmethod"), data.s3);
			});								
		}*/
	});	
	//
	$("#parentId").change(function() {
		var parentId = $(this).val();
		//alert(parentId);
		//load billing options
		//if(parentId.length > 0) {
			var url = jsBaseUrl+"/ajaxrequest/loadbillingoptions?id="+parentId;
			$.getJSON(url, function(data){
				//alert(data.toSource());
				$("#billing_address").val(data.address);
				$("#billing_address_2").val(data.address2);				
				$("#billing_country").val(data.country);				
				$("#billing_city").val(data.city);				
				$("#billing_state").val(data.state);				
				$("#billing_zip").val(data.zipcode);				
			});	
		//}
	});
	//
	$("#lshippingcompany").change(function() {
		//alert($(this).val());
		loadShippingMethods($(this), $('#l-add-location-form #lshippingmethod'), "");
	});
	//add location
	$('#l-add-location-form').submit(function(event) {
		var e = $(this);
		e.find('.col-sm-6').removeClass('has-error'); // remove the error class
		e.find('.col-sm-3').removeClass('has-error'); // remove the error class
		e.find('.col-sm-4').removeClass('has-error'); // remove the error class
		e.find('.help-block').remove(); // remove the error text
		//
		var formData = {
			'customerid' : "",
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
			'lshippingmethod' : $('#lshippingmethod').val(),
			'_csrf' : jsCrsf
		};
		/* $.each(
        formData,
        function(i, val){
            alert(val.storenum);
            alert(val.storename);
            alert(val.address);
            alert(val.address2);
            alert(val.country);
            alert(val.city);
            alert(val.state);
        }                   
    );*/
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

						//$('#location_email-group').append('<div class="help-block">' + data.errors.eemail + '</div>'); // add the actual error message under our input

					}

					if (data.errors.eaddress) {
						
						$('#location_address-group').addClass('has-error'); // add the error class to show red input

						//$('#location_address-group').append('<div class="help-block">' + data.errors.eaddress + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ecountry) {
						
						$('#location_country-group').addClass('has-error'); // add the error class to show red input

						//$('#location_country-group').append('<div class="help-block">' + data.errors.ecountry + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ecity) {
						
						$('#location_city-group').addClass('has-error'); // add the error class to show red input

						//$('#location_city-group').append('<div class="help-block">' + data.errors.ecity + '</div>'); // add the actual error message under our input

					}

					if (data.errors.estate) {
						
						$('#location_state-group').addClass('has-error'); // add the error class to show red input

						//$('#location_state-group').append('<div class="help-block">' + data.errors.estate + '</div>'); // add the actual error message under our input

					}

					if (data.errors.ezip) {
						
						$('#location_zip-group').addClass('has-error'); // add the error class to show red input

						//$('#location_zip-group').append('<div class="help-block">' + data.errors.ezip + '</div>'); // add the actual error message under our input

					}																																													
				}
				else
				{
					Loadlocations(0, $("#selectShippinglocation"), data.id);
					//load location items for customer
					$('#l-add-location-form')[0].reset();
					e.find('#lshippingmethod').select2('val', '');
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
	//
    jQuery.validator.addMethod("billingAddress", function(value, element) {
        var isVisible = $('#billingAddress').is(':visible');
        alert(isVisible);
        if(!isVisible)
        return false;
        else
        return true;
    }, "Address is required.");
    
    jQuery.validator.addMethod("billingCountry", function(value, element) {
        return false;
    }, "Is required.");
    
    jQuery.validator.addMethod("billingCity", function(value, element) {
        return false;
    }, "Is required.");
    
    jQuery.validator.addMethod("billingState", function(value, element) {
        return false;
    }, "Is required.");
    
    jQuery.validator.addMethod("billingZip", function(value, element) {
        return false;
    }, "Is required.");
	var validaterules = {
		required: true
	}
	if($('#customer_Id').val().length === 0)
	{
		validaterules = {
			required: true,
			checkCustomerCode: true
		}
		jQuery.validator.addMethod("checkCustomerCode", 
			function(value, element) {
				var result = false;
				$.ajax({
					async: false,
					url: jsBaseUrl+"/ajaxrequest/verifycustomercode", // script to validate in server side
					data: {code: value},
					success: function(data) {
						var res = parseInt(data);
						//alert(data.toSource());
						result = (res!="1") ? true : false;
					}
				});
				// return true if username is exist in database
				return result; 
			}, 
			"Code is already taken!"
		);
	}
	
	$("#c1_shippingcompany").change(function() {
		//alert($(this).val());
		loadShippingMethods($(this), $('#defaultshippingmethod'), "");
	});
	//
	$("#c2_shippingcompany").change(function() {
		//alert($(this).val());
		loadShippingMethods($(this), $('#secondaryshippingmethod'), "");
	});

	$('#create-customer-form').validate({
		rules: {
			
			customercode: validaterules,
			companyname: "required",
			email: {
				required: true,
				email: true
			},
			defaultaccountnumber: "required",
			c1_shippingcompany: {
				required: true,
			},
			/* shipping_address: "required",
			shipping_country: "required",
			shipping_city: "required",
			shipping_state: "required",
			shipping_zip: "required", */
			billing_address: {
				required: true,
				//billingAddress: true
			},
			billing_country: {
				required: true,
				//billingCountry: true
			},
			billing_city: {
				required: true,
				//billingCity: true
			},
			billing_state: {
				required: true,
				//billingState: true
			},
			billing_zip: {
				required: true,
				//billingZip: true
			},
		},
		messages: {
			defaultaccountnumber: "Default Account Number required !",
			//defaultshippingmethod: "Default Shipping Method required !",
			customercode: {
				required: "Please enter customer code",
				checkCustomerCode: "Code is already taken!"
			},
			companyname: "Please enter your company name",
			email: "Please enter a valid email address",
			/* shipping_address: "Address is required.",
			shipping_country: "Country is required.",
			shipping_city: "City is required.",
			shipping_state: "State is required.",
			shipping_zip: "Zip is required.", */
			billing_address: "Address is required.",
			billing_country: "Country is required.",
			billing_city: "City is required.",
			billing_state: "State is required.",
			billing_zip: "Zip is required.",
			c1_shippingcompany: "Default Shipping Company required !",
		},
		submitHandler: function (form) {
			form.submit();
		}
	});	
	
	$('#create-customer-form').submit(function(e) {

		e.preventDefault();
	});
	//add customer
	$('#add-customer-form').submit(function(event) {
		var requireOrderIsChecked = $('#requireordernumber:checkbox:checked').length > 0;
		var requireSerialNumberIsChecked = $('#trackserials:checkbox:checked').length > 0;
		var requireOrder = false;
		var requireSerialNumber = false;
		if(requireOrderIsChecked)
			requireOrder = true;
		if(requireSerialNumberIsChecked)
			requireSerialNumber = true;
		//
		$('.col-sm-6').removeClass('has-error'); // remove the error class
		$('.col-sm-4').removeClass('has-error'); // remove the error class
		$('.col-sm-3').removeClass('has-error'); // remove the error class
		$('.help-block').remove(); // remove the error text
		// get the form data
		// there are many ways to get this data using jQuery (you can use the class or id also)
		var formData = {
			'company_name' : $('#companyname').val(),
			//'contact_name' : $('#contactname').val(),
			'firstname' : $('#firstname').val(),
			'lastname' : $('#lastname').val(),
			'defaultaccountnumber' : $('#defaultaccountnumber').val(),
			'defaultshippingmethod' : $('#defaultshippingmethod').val(),
			'email' : $('#customeremail').val(),
			'phone' : $('#customerphone').val(),
			'phone' : $('#customerphone').val(),
			'shipping_address' : $('#shipping_address').val(),
			'shipping_country' : $('#shipping_country').val(),
			'shipping_city' : $('#shipping_city').val(),
			'shipping_state' : $('#shipping_state').val(),
			'shipping_zip' : $('#shipping_zip').val(),
			'moreCustomerAddressInfo' : $('#billing_required').val(),
			'billing_address' : $('#billing_address').val(),
			'billing_country' : $('#billing_country').val(),
			'billing_city' : $('#billing_city').val(),
			'billing_state' : $('#billing_state').val(),
			'billing_zip' : $('#billing_zip').val(),
			'require_order' : requireOrder,
			'require_serial_number' : requireSerialNumber,
			'receiving_location' : $('#defaultreceivinglocation').val(),
			'_csrf' : jsCrsf
		};
		// process the form
		$.ajax({
			type        : 'POST', // define the type of HTTP verb we want to use (POST for our form)
			url         : jsBaseUrl+"/ajaxrequest/addcustomer", // the url where we want to POST
			data        : formData, // our data object
			dataType    : 'json', // what type of data do we expect back from the server
			encode          : true
		})
			// using the done promise callback
		 .done(function(data) {
					if ( ! data.success) {
						// handle errors for email ---------------
						if (data.errors.ecompanyname) {
		
							$('#companyname-group').addClass('has-error'); // add the error class to show red input
		
							$('#companyname-group').append('<div class="help-block">' + data.errors.ecompanyname + '</div>'); // add the actual error message under our input
		
						}

						/*if (data.errors.econtactname) {
							
							$('#contactname-group').addClass('has-error'); // add the error class to show red input
		
							$('#contactname-group').append('<div class="help-block">' + data.errors.econtactname + '</div>'); // add the actual error message under our input
		
						}*/
					
						if (data.errors.efirstname) {
							
							$('#firstname-group').addClass('has-error'); // add the error class to show red input
		
							$('#firstname-group').append('<div class="help-block">' + data.errors.efirstname + '</div>'); // add the actual error message under our input
		
						}
						
						if (data.errors.elastname) {
							
							$('#lastname-group').addClass('has-error'); // add the error class to show red input
		
							$('#lastname-group').append('<div class="help-block">' + data.errors.elastname + '</div>'); // add the actual error message under our input
		
						}
						
						if (data.errors.edefaultaccountnumber) {
							
							$('#defaultaccountnumber-group').addClass('has-error'); // add the error class to show red input
		
							$('#defaultaccountnumber-group').append('<div class="help-block">' + data.errors.edefaultaccountnumber + '</div>'); // add the actual error message under our input
		
						}
						
						if (data.errors.edefaultshippingmethod) {
							
							$('#defaultshippingmethod-group').addClass('has-error'); // add the error class to show red input
		
							$('#defaultshippingmethod-group').append('<div class="help-block">' + data.errors.edefaultshippingmethod + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eemail) {
							
							$('#email-group').addClass('has-error'); // add the error class to show red input
		
							$('#email-group').append('<div class="help-block">' + data.errors.eemail + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.ephone) {
							
							$('#phone-group').addClass('has-error'); // add the error class to show red input
		
							$('#phone-group').append('<div class="help-block">' + data.errors.ephone + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eshippingaddress) {
							
							$('#shipping_address-group').addClass('has-error'); // add the error class to show red input
		
							$('#shipping_address-group').append('<div class="help-block">' + data.errors.eshippingaddress + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eshippingcountry) {
							
							$('#shipping_country-group').addClass('has-error'); // add the error class to show red input
		
							$('#shipping_country-group').append('<div class="help-block">' + data.errors.eshippingcountry + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eshippingcity) {
							
							$('#shipping_city-group').addClass('has-error'); // add the error class to show red input
		
							$('#shipping_city-group').append('<div class="help-block">' + data.errors.eshippingcity + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eshippingstate) {
							
							$('#shipping_state-group').addClass('has-error'); // add the error class to show red input
		
							$('#shipping_state-group').append('<div class="help-block">' + data.errors.eshippingstate + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.eshippingzip) {
							
							$('#shipping_zip-group').addClass('has-error'); // add the error class to show red input
		
							$('#shipping_zip-group').append('<div class="help-block">' + data.errors.eshippingzip + '</div>'); // add the actual error message under our input
		
						}																																				

						if (data.errors.ebillingaddress) {
							
							$('#billing_address-group').addClass('has-error'); // add the error class to show red input
		
							$('#billing_address-group').append('<div class="help-block">' + data.errors.ebillingaddress + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.ebillingcountry) {
							
							$('#billing_country-group').addClass('has-error'); // add the error class to show red input
		
							$('#billing_country-group').append('<div class="help-block">' + data.errors.ebillingcountry + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.ebillingcity) {
							
							$('#billing_city-group').addClass('has-error'); // add the error class to show red input
		
							$('#billing_city-group').append('<div class="help-block">' + data.errors.ebillingcity + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.ebillingstate) {
							
							$('#billing_state-group').addClass('has-error'); // add the error class to show red input
		
							$('#billing_state-group').append('<div class="help-block">' + data.errors.ebillingstate + '</div>'); // add the actual error message under our input
		
						}

						if (data.errors.ebillingzip) {
							
							$('#billing_zip-group').addClass('has-error'); // add the error class to show red input
		
							$('#billing_zip-group').append('<div class="help-block">' + data.errors.ebillingzip + '</div>'); // add the actual error message under our input
		
						}										
					}
					else
					{
						$('#add-customer-form')[0].reset();
						$('div[id^="demo-file"]').html('');
						$('#customer-error').show();
						$('#customer-error').html(data.message);
						$("#customer-error").delay(2000).fadeOut("slow", function () { 
							$("#customer-error").hide(); 
							$('#addCustomer').modal('hide');
						});
						$('#add-customer-form').hide();
					}
	   });

		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
	});

	//
	$("#collapsingbutton-billing").click(function() {
		var billing_state = $('#billing_required').val();
		if(billing_state==0)
			$('#billing_required').val(1);
		else 
			$('#billing_required').val(0);
	});
	
	$('#closeCustomerDialog').click(function(){
		//reset customer add form
		$('#add-customer-form')[0].reset();
	});

});		