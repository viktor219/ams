$(function () {
	$('#btnAdd').click(function () {
		var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
	// manipulate the name/id values of the input inside the new element
 
		// Title - select
		newElem.find('.select_ttl').attr('id', 'ID' + newNum + '_title').attr('name', 'ID' + newNum + '_title').val('');
 
		// First name - text
		newElem.find('.input_fn').attr('id', 'ID' + newNum + '_first_name').attr('name', 'ID' + newNum + '_first_name').val('');
 
	// insert the new element after the last "duplicatable" input field
		$('#entry' + num).after(newElem);
		$('#ID' + newNum + '_title').focus();
 
	// enable the "remove" button
		$('#btnDel').attr('disabled', false);
 
	// right now you can only add 5 sections. change '5' below to the max number of times the form can be duplicated
		if (newNum == 10)
		$('#btnAdd').attr('disabled', true).prop('value', "You've reached the limit");
	});
 
	$('#btnDel').click(function () {
	// confirmation
				var num = $('.clonedInput').length;
				// how many "duplicatable" input fields we currently have
				$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
				// if only one element remains, disable the "remove" button
					if (num -1 === 1)
				$('#btnDel').attr('disabled', true);
				// enable the "add" button
				$('#btnAdd').attr('disabled', false).prop('value', "add section");});
		return false;
			 // remove the last element
 
	// enable the "add" button
		$('#btnAdd').attr('disabled', false);
	});
 
	$('#btnDel').attr('disabled', true);
});