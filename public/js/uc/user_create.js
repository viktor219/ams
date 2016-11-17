//Loadcustomers($('#selectCustomers'));
$("#u_usertype").change(function() {
	var e = $(this);
	//alert(e.val());
	if(e.val() == 1 || e.val() == 9)
		$('#user-customer-group').show();
	else 
		$('#user-customer-group').hide();
});
/**
 * JS event module to validate each field requried
 */
$(document).ready(function () {
	$('#add-user-form').submit(function(event) {
		$('.col-sm-6').removeClass('has-error');
		var u_email = $('#u_email').val();
		var u_firstname = $('#u_firstname').val();
		var u_lastname = $('#u_lastname').val();
		var u_username = $('#u_username').val();
		var u_password = $('#u_hash_password').val();
		
		var error = 0;

		if (u_email.length == 0) {
			$('#user_email_group').addClass('has-error'); // add the error class to show red input
			error++;
		} 	
		
		if (u_firstname.length == 0) {
			$('#user_firstname_group').addClass('has-error'); // add the error class to show red input
			error++;
		} 	

		if (u_lastname.length == 0) {
			$('#user_lastname_group').addClass('has-error'); // add the error class to show red input
			error++;
		} 

		if (u_username.length == 0) {
			$('#user_username_group').addClass('has-error'); // add the error class to show red input
			error++;
		} 
		
		if (u_password.length == 0) {
			$('#user_password_group').addClass('has-error'); // add the error class to show red input
			error++;
		}

		if ($('#user_type_group').length > 1 && !$('#u_usertype').val()) {
			$('#user_type_group').addClass('has-error'); // add the error class to show red input
			error++;
		}
		
		if(error) {
			event.preventDefault();
			return false;
		}	
	});
});
//
/*$("#add-user-form").validate({
	rules: {
		u_email: {
			required: true,
			email: true
		},
		
		u_firstname: "required",
		u_lastname: "required",
		u_username: "required",
		u_hash_password: {
			required: true,
			minlength: 6,
		},
		u_usertype: {
			required: true,
			min: 1
		}
	},
	messages: {
		u_email: "Email is required.",
		u_firstname: "First Name is required.",
		u_lastname: "Last Name is required.",
		u_username: "Username is required.",
		u_hash_password: {
			required: 'Password is required!',
			minlength: 'Password length must be at least 6.',
		},
		u_usertype: "User Type is required.",
	},
	submitHandler: function (form) {
		form.submit();
	}
});*/