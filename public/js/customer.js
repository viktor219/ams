	function loadSearchCustomer(url)
	{
            
            $.ajax({
			url: url,
			dataType: "json",
			beforeSend: function() { $('#loading').show(); $("#customer-loaded-content-search").children().prop('disabled',true);},
			complete: function() { 
				$('#loading').hide(); 
				$("#customer-loaded-content-search").children().prop('disabled',false);			
				//pagination 
				$('#customer-search-gridview .pagination a').each(function() {
                                        $(this).attr('onClick', 'loadSearchCustomer("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
				//sorting
				$('#customer-search-gridview thead th a').each(function() {
					$(this).attr('onClick', 'loadSearchCustomer("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 				
			}
		}).done(function (data) {
			//alert(data);
			if (data.success) {
				$(".customer-results-count").html(data.count);
				$("#customer-loaded-content-search").html(data.html);
                                $('table').stacktable();
			}
		});
	}
        
        function deleteCustomers(url){
            if(url=="")
		url = jsBaseUrl+"/customers/getdeleted";
            $.ajax({
			url: url,
			dataType: "json",
			beforeSend: function() { $('#loading').show(); $("#customer-deleted-content").children().prop('disabled',true);},
			complete: function() { 
				$('#loading').hide(); 
				$("#customer-deleted-content").children().prop('disabled',false);			
				//pagination 
				$('#customer-deleted-content .pagination a').each(function() {
					$(this).attr('onClick', 'deleteCustomers("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
				//sorting
				$('#customer-deleted-content thead th a').each(function() {
					$(this).attr('onClick', 'deleteCustomers("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
                                $(".deleteCustomer").click(function(){
                                    var href = $(this).attr('href');
                                    $('#deleteConfirm').modal('show');
                                    $('#yes-delete-order').attr('href', href);
                                    return false;
                                });  
                                $(".revertCustomer").click(function(){
                                    var href = $(this).attr('href');
                                    $.ajax({
                                        url: href,
                                        success: function(data){
                                            if(data){
                                              deleteCustomers('');
                                            } 
                                        }
                                    });
                                    return false;
                                });                            
			}
		}).done(function (data) {
			//alert(data);
			if (data.success) {
				$("#customer-deleted-content").html(data.html);
                                $('.total_delete_count').html(data.count);
                                $('#customer-deleted-content .pagination a, #customer-deleted-content thead th a').click(function() {
                                    return false; 
                                });    
                                $("#customer-deleted-content table").stacktable();
			}
		});
        }
        
        function loadCustomers(url){
            if(url==""){
		url = jsBaseUrl+"/customers/load";
            }
            $.ajax({
			url: url,
			dataType: "json",
			beforeSend: function() { $('#loading').show(); $("#main-gridview").children().prop('disabled',true);},
			complete: function() { 
				$('#loading').hide(); 
				$("#main-gridview").children().prop('disabled',false);			
				//pagination 
				$('#main-gridview .pagination a').each(function() {
					$(this).attr('onClick', 'loadCustomers("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
				//sorting
				$('#main-gridview thead th a').each(function() {
					$(this).attr('onClick', 'loadCustomers("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
                                $(".deleteCustomer").click(function(){
                                    var href = $(this).attr('href');
                                    $('#deleteConfirm').modal('show');
                                    $('#yes-delete-order').attr('href', href);
                                    return false;
                                });  
                                $(".revertCustomer").click(function(){
                                    var href = $(this).attr('href');
                                    $.ajax({
                                        url: href,
                                        success: function(data){
                                            if(data){
                                              deleteCustomers('');
                                            } 
                                        }
                                    });
                                    return false;
                                });
			}
		}).done(function (data) {
			//alert(data);
			if (data.success) {
				$("#main-gridview").html(data.html);
                                $('#main-gridview table').stacktable();
			}
		});
        }
$('document').ready(function () {
    loadCustomers('');
    $(".viewCustomer").on('click', function () {

        var customerId = $(this).attr('cid')
        loadCustomerDetails(customerId);
    });
    $(".deleteCustomer").on('click', function () {
        if (!confirm("Are you sure to delete this customer?"))
            return false;
        var customerId = $(this).attr('cid');
        window.location = jsBaseUrl+"/customers/default/delete?id="+customerId;
    });
    
    $(".createCustomer").on('click', function () {
        loadCustomerCreationForm();
    });

    $(".updateCustomer").on('click', function () {

        var customerId = $(this).attr('cid')
        loadCustomerUpdateForm(customerId);
    });

    $(".btnShowAllProject").on('click', function () {

        var projectId = $(this).attr('id')
        loadAllProjects(projectId);
    });


    $(".btnAddProject").on('click', function () {
        var presentprojectId = $(this).attr('id')
        loadProjectAddForm(presentprojectId);
    });
     
    $(document).on('click', '.assignUser', function (e) {


       loadAssignUserForm();
        
    });
    
    function loadAssignUserForm() {
        
        $.ajax({
            url: jsBaseUrl+"/customers/default/assignuser",
            data: {
                id: 0
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                
                $("#assignUserForm").html(data.html);
                $("#assignUser").modal('show');
                
                $("#assignUserRegister").validate({
                    rules: {
                        
                        userId: "required",
                        projectId: "required",
                    },
                    messages: {
                        userId: "User is required.",
                        projectId: "Project is required."
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                
                
                
            }
        });
    }
    
    function loadProjectAddForm(presentprojectId) {
        $.ajax({
            url: jsBaseUrl+"/customers/default/addproject",
            data: {
                id: presentprojectId
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#projectAddForm").html(data.html);
                $("#projectAdd").modal('show');
                 
            
                $("#projectAddRegister").validate({
                    rules: {
                        companyname: "required",
                    },
                    messages: {
                        companyname: "Please enter your company name"
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                
                
                
            }
        });
    }
    


    /*$(".collapsingbutton-billing").on('click', function () {

        $("#customerDetails").modal('hide');
        $("#customerDetailsUpdate").modal('show');
        
    });*/
    

    jQuery.validator.addMethod("billingAddress", function(value, element) {
        var isVisible = $('#billingAddress').is(':visible');
        //alert(isVisible);
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

    function loadCustomerUpdateForm(id) {
        $.ajax({
            url: jsBaseUrl+"/customers/default/update",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#customerUpdateForm").html(data.html);
                $("#customerUpdate").modal('show');
                 
            
                $("#customerUpdateRegisterForm").validate({
                    rules: {
						customercode: "required",
                        companyname: "required",
                        email: {
                            required: true,
                            email: true
                        },
                        
                        shipping_address: "required",
                        shipping_country: "required",
                        shipping_city: "required",
                        shipping_state: "required",
                        shipping_zip: "required",
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
						customercode: "Please enter customer code",
                        companyname: "Please enter your company name",
                        email: "Please enter a valid email address",
                        shipping_address: "Address is required.",
                        shipping_country: "Country is required.",
                        shipping_city: "City is required.",
                        shipping_state: "State is required.",
                        shipping_zip: "Zip is required.",
                        billing_address: "Address is required.",
                        billing_country: "Country is required.",
                        billing_city: "City is required.",
                        billing_state: "State is required.",
                        billing_zip: "Zip is required.",
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                
                
                
            }
        });
    }
	
    function loadCustomerCreationForm() {
        $.ajax({
            url: jsBaseUrl+"/customers/default/create",
            data: {
                id: 0
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#customerCreationForm").html(data.html);
                $("#customerCreation").modal('show');
                 
            
                $("#customerRegisterForm").validate({
                    rules: {
						
						customercode: "required",
                        companyname: "required",
                        email: {
                            required: true,
                            email: true
                        },
                        defaultaccountnumber: "required",
						defaultshippingmethod: "required",
                        shipping_address: "required",
                        shipping_country: "required",
                        shipping_city: "required",
                        shipping_state: "required",
                        shipping_zip: "required",
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
						defaultshippingmethod: "Default Shipping Method required !",
						customercode: "Please enter customer code",
                        companyname: "Please enter your company name",
                        email: "Please enter a valid email address",
                        shipping_address: "Address is required.",
                        shipping_country: "Country is required.",
                        shipping_city: "City is required.",
                        shipping_state: "State is required.",
                        shipping_zip: "Zip is required.",
                        billing_address: "Address is required.",
                        billing_country: "Country is required.",
                        billing_city: "City is required.",
                        billing_state: "State is required.",
                        billing_zip: "Zip is required.",
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                
                
                
            }
        });
    }
    
    function loadCustomerDetails(id) {
        $.ajax({
            url: jsBaseUrl+"/customers/default/view",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#detaisOfCustomer").html(data.html);
                $("#customerDetails").modal('show');
            }
        });
    }
    
    function loadAllProjects(id) {
        $.ajax({
            url: jsBaseUrl+"/customers/default/showallprojects",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#detaisOfshowAllProjects").html(data.html);
                $("#showAllProjects").modal('show');
            }
        });
    }
	//
	//TODO : will be removed when load customer is normally made.
	/*function loadCustomers()
	{
		$('#loading').show(); 
		$("#customer-loaded-content").children().prop('disabled',true);
		$("#customer-loaded-content").load(jsBaseUrl+"/customers/load", function(responseText, textStatus, jqXHR){
			//
			var hideAllPopovers = function() {
				$('.popup-marker').each(function() {
					$(this).popover('destroy');
				}); 
			};
			//$(this).find('script').remove();
			//Do whatever you want here              
			$('#loading').hide(); 
			$("#customer-loaded-content").children().prop('disabled',false);		
			//load core functions
			$.getScript(jsBaseUrl+"/public/js/functions.js", function( data, textStatus, jqxhr ) {});	
			//load tooltip
			$.getScript(jsBaseUrl+"/public/js/tooltip.js", function( data, textStatus, jqxhr ) {});
			//load popover
			$.getScript(jsBaseUrl+"/public/js/popover.js", function( data, textStatus, jqxhr ) {});
			//
			$('[id^="item-popover_"]').hover(function() {
				var e=$(this);
				e.off('hover');
				$.get(e.data('poload'),function(d) {
					//alert(d);
					hideAllPopovers();
					e.popover().popover('show'); 
				});
			});
			//
			$('[id^="item-popover_"]').on( "mouseleave", function() {
				hideAllPopovers();
			});
			
			$('[id^="item-popover_"]').focusout(function() {
				hideAllPopovers();
			});		
		});	
	}	*/
	//
	$(document).on("click touchstart", '#searchCustomerBtn', function(event) { 
		if($('#searchCustomer').val().length===0)
			alert('Search field value missing!');
		else{
			$('#main-gridview').hide();
			$('#search-gridview').show();			
			//hide list gridview
			$('#customer-main-gridview').hide();
			//show search gridview
			$('#customer-search-gridview').show();		
			//process order search 
			searchCustomer($('#searchCustomer').val());	
		}
	});	
	//
	$(document).on("keyup", '#searchCustomer', function(event) { 
		var inputContent = $(this).val();
		if(event.keyCode != 46) {
			if( (inputContent.length > 1)) {
				//hide list gridview
				$('#main-gridview').hide();
				$('#search-gridview').show();			
				//hide list gridview
				$('#customer-main-gridview').hide();
				//show search gridview
				$('#customer-search-gridview').show();
				//process inventory search 
				searchCustomer(inputContent);	
			}
		}
		else if(inputContent=="")
			loadCustomers();
		else
			event.preventDefault();
	});
	//pagination fix
	/*$(document).on('click', '#customer-main-gridview .pagination a', function() {
		loadSCustomers($(this).attr('data-href'));
		return false;
	});
	//sort fix 
	$(document).on('click', '#customer-main-gridview thead a', function() {
		loadSCustomers($(this).attr('data-href'));
		return false;
	});	*/
	//
	$(document).on("keydown", '#searchCustomer', function(event) { 
		if( (event.keyCode == 13)) {
			$('#main-gridview').hide();
			$('#search-gridview').show();			
			//hide list gridview
			$('#customer-main-gridview').hide();
			//show search gridview
			$('#customer-search-gridview').show();		
			//process customer search 
			searchCustomer($(this).val());	
			//
			event.preventDefault();
			return false;
		}
	});
	//
	function searchCustomer(query)
	{
		$.ajax({
			url: jsBaseUrl+"/customers/search",
			data: {
				query: query
			},
			dataType: "json",
			beforeSend: function() { $('#loading').show(); $("#customer-loaded-content-search").children().prop('disabled',true);},
			complete: function() { 
				$('#loading').hide(); 
				$("#customer-loaded-content-search").children().prop('disabled',false);			
				//pagination 
				$('#customer-search-gridview .pagination a').each(function() {
                                        $(this).attr('onClick', 'loadSearchCustomer("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 
				//sorting
				$('#customer-search-gridview thead th a').each(function() {
					$(this).attr('onClick', 'loadSearchCustomer("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				}); 	
                                $(".deleteCustomer").click(function(){
                                    var href = $(this).attr('href');
                                    $('#deleteConfirm').modal('show');
                                    $('#yes-delete-order').attr('href', href);
                                    return false;
                                });                                  
			}
		}).done(function (data) {
			//alert(data);
			if (data.success) {
				$(".customer-results-count").html(data.count);
				$("#customer-loaded-content-search").html(data.html);
                                $('table').stacktable();
			}
		});	
		//e.preventDefault();
	}
        //pagination
        $('#main-gridview .pagination a').each(function() {
                    $(this).attr('onClick', 'loadCustomers("' + $(this).attr('href') + '");');
                    $(this).attr('href', 'javascript:void(0);');
	}); 
        //sorting
        $('#main-gridview thead th a').each(function() {
                $(this).attr('onClick', 'loadCustomers("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
        }); 
        $('#main-gridview .pagination a, #main-gridview thead th a').click(function() {
            return false; 
        });         
        
});