$(document).ready(function() {
	// process the form
	//add location
	$('#reorder-form').submit(function(event) {
		$('#rqty-group').removeClass('has-error'); // remove the error class
		$('.help-block').remove(); // remove the error text
		var rqty = $('input[name=rqty]').val();
	//
		if (rqty.length == 0) {
			$('#rqty-group').addClass('has-error'); // add the error class to show red input
			$('#rqty-group').append('<div class="help-block">Quantity field is required!</div>'); // add the actual error message under our input
		}else {
			$('#reorder-form')[0].submit();
		}
		// stop the form from submitting the normal way and refreshing the page
		event.preventDefault();
		//
		return  false;
	});		
});		