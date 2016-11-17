	$('[id^="assembly-popover_"]').hover(function() {
		var e=$(this);
		e.popover('show'); 
	});	
	//
	var __aloadeditems;
	$('#autocomplete-assembly-customer').typeahead({
		onSelect: function(item) {
			var items="";
			customerid=item.value;
			$("#customer_Id").val(customerid);
			$("#autocompleteitem_1").typeahead('destroy');
			//
			$.get(jsBaseUrl+"/ajaxrequest/loadmodels?customer=" + customerid, function(data){
				__aloadeditems = data;
				$("#autocompleteitem_1").typeahead({ 
					onSelect: function(item) {
						//---- get model selected id
						$('#autocompletevalitem_1').val(item.value);
						//----
					},				
					source:data,
					//autoSelect: true,
					items : 10	
				});
			},'json');	
		},
		ajax: jsBaseUrl+"/ajaxrequest/listcountries?query="+$('#autocomplete-assembly-customer').val(),
		items : 10
	});
	//
	$('#submitAssembly').click(function (event) {
		$('.qty-group').removeClass('has-error');
		$('.desc-group').removeClass('has-error');
		$('#modelId-group').removeClass('has-error');
		$('#customer-group').removeClass('has-error');
		$('.help-block').remove(); // remove the error text
		//
		var vassemblyname = $('input[name=assembly_name]').val();
		var vcustomer = $('input[name=customer]').val();
		var qtys = $(".select_ttl").children();
		var items = $(".input_h").children();
		var error = 0;
		//
		if (vcustomer.length == 0) {
			$('#customer-group').addClass('has-error'); // add the error class to show red input
			$('#customer-group').append('<div class="help-block">Customer field is required!</div>'); // add the actual error message under our input
			error++;
		} 
		//
		if (vassemblyname.length == 0) {
			$('#modelId-group').addClass('has-error'); // add the error class to show red input
			$('#modelId-group').append('<div class="help-block">Assembly name field is required!</div>'); // add the actual error message under our input
			error++;
		}		
		//
		$(".select_ttl").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");
				$('#' + id).parents("div").eq(0).addClass( "has-error" ); // add the error class to show red input
				error++;
			}
		});
		//
		$(".input_h").each(function(i){
			if(this.value=="") { 
				id  = this.getAttribute("id");
				$('#' + id).parents("div").eq(0).addClass("has-error"); // add the error class to show red input
				$('#' + id).parents("div").eq(0).append('<div class="help-block">Item field is required!</div>'); // add the error class to show red input
				error++;
			}
		});	
		if(!error)
			$('#add-assembly-form')[0].submit();
		event.preventDefault();
		return  false;
	});
	
//});

$(function () {
	$('#btnAAdd').click(function () {
		//alert(__aloadeditems);
		var num     = $('.clonedInput').length, // how many "duplicatable" input fields we currently have
			newNum  = new Number(num + 1),      // the numeric ID of the new input field being added
			newElem = $('#entry' + num).clone().attr('id', 'entry' + newNum).fadeIn('slow'); // create the new element via clone(), and manipulate it's ID using newNum value
		// manipulate the name/id values of the input inside the new element
		
		newElem.find('.input_partnum').attr('id', 'partnumber_' + newNum).val('');
		
		newElem.find('.select_ttl').attr('id', 'quantity_' + newNum).attr('name', 'quantity[]').val('');
		
		newElem.find('.input_fn').attr('id', 'autocompleteitem_' + newNum).attr('name', 'description[]').val('').removeAttr('readonly');
		
		newElem.find('.input_h').attr('id', 'autocompletevalitem_' + newNum).val('');
		
		newElem.find('.qty-group').removeClass('has-error');
		
		newElem.find('.desc-group').removeClass('has-error');
		
		newElem.find('.partNumber-group').removeClass('has-error');
				
		newElem.find('.help-block').remove();
 
	// insert the new element after the last "duplicatable" input field
		$('#entry' + num).after(newElem);
		//$('#autocompleteitem_' + newNum).focus();
 
	// enable the "remove" button
		$('#btnADel').attr('disabled', false);	
		//
		//customer = $("#autocomplete-assembly-customer").val();
		//var url_models = jsBaseUrl+"/ajaxrequest/loadmodels?customer="+customer;
		//$.get(url_models, function(data){
			$('#autocompleteitem_' + newNum).typeahead({ 
				onSelect: function(item) {
					$('#autocompletevalitem_' + newNum).val(item.value);
				},				
				source:__aloadeditems,
				//autoSelect: true,
				items : 10	
			});
		//},'json');
	});
 
	$('#btnADel').click(function () {
		// confirmation
			var num = $('.clonedInput').length;
			var current_num = num -1;
		// how many "duplicatable" input fields we currently have
			$('#entry' + num).slideUp('fast', function () {$(this).remove(); 
		// if only one element remains, disable the "remove" button
			if ( current_num === 1)
				$('#btnADel').attr('disabled', true);
		// enable the "add" button
			$('#btnAAdd').attr('disabled', false).prop('value', "add section");});
		// remove the last element
	 
		$('#btnADel').attr('disabled', true);
	});
	
	$('#btnADel').attr('disabled', true);
});