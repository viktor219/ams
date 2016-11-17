$('#modelCustomer_1').typeahead({
	onSelect: function(item) {
		customerid=item.value;
		$('#modelCustomerval_1').val(customerid);
	},
	ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#modelCustomer_1').val(),
	items : 10
});
$('#autocomplete-aei').typeahead({
	onSelect: function(item) {
	},
	ajax: jsBaseUrl+"/ajaxrequest/listaei?query="+$('#autocomplete-aei').val(),
	items : 10
});
//
//add button
$('#PartbtnAdd').click(function () {
		var num     = $('.partClonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#partEntry' + num).clone().attr('id', 'partEntry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value							
		//alert(num);
	//
		newElem.find('.input_cust').attr('id', 'modelCustomer_' + newNum).attr('name', 'modelCustomer[]').val('');
		
		newElem.find('.input_h').attr('id', 'modelCustomerval_' + newNum).attr('name', 'modelCustomerval[]').val('');
		
		newElem.find('.input_partid').attr('id', 'partid_' + newNum).attr('name', 'partid[]').val('');
		
		newElem.find('.input_partdesc').attr('id', 'partdesc_' + newNum).attr('name', 'partdesc[]').val('');
	//
		newElem.find('.partdesc-group').removeClass('has-error');
	
		newElem.find('.customer-group').removeClass('has-error');
								
		newElem.find('.partid-group').removeClass('has-error');
	
		newElem.find('.help-block').remove();							
	//
		$('#partEntry' + num).after(newElem);
		$('#modelCustomer_'+newNum).focus();
	//load customers 
		$('#modelCustomer_'+newNum).typeahead({
			onSelect: function(item) {
				$('#modelCustomerval_'+newNum).val(item.value);
			},
			ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#modelCustomer_'+newNum).val(),
			items : 10
		});	
	// enable the "remove" button
		$('#PartbtnDel').attr('disabled', false);
	// right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
		if (newNum == 10)
		$('#PartbtnAdd').attr('disabled', true).prop('value', "You've reached the limit");								
});
//delete button
$('#PartbtnDel').click(function () {
	// confirmation
		var num = $('.partClonedInput').length;
		var current_num = num -1;
	// how many "duplicatable" input fields we currently have
		$('#partEntry' + num).slideUp('fast', function () {$(this).remove(); 
	// if only one element remains, disable the "remove" button
		if ( current_num === 1)
			$('#PartbtnDel').attr('disabled', true);
	// enable the "add" button*
		$('#PartbtnAdd').attr('disabled', false).prop('value', "add section");});
	// remove the last element
});
//
$('#PartbtnDel').attr('disabled', true);
//
$('#inventory-add-model-form').validate({
	rules: {		
		manufacturer: "required",
		category: "required",
		department: "required",
		descrip: "required",
		//purchasepricing: "required",
		//repairpricing: "required"
	},
	messages: {
		manufacturer: "Please select manufacturer",
		category: "Please select category",
		department: "Please select  department",
		descrip: "Description is required",
		//purchasepricing: "Purchase pricing is required.",
		//repairpricing: "Repair pricing is required."
	},
	submitHandler: function (form) {
		if($('#serialized').prop("checked")){
			$('#serialized').val(1);
		} else {
			$('#serialized').val(0);
		}
		form.submit();
	}
});