    /*$(document).on('click', '.loadUsersType', function () {
        var typeId = $(this).attr('uid');
        var userType = $(this).html().trim();
        $('#myTab ul li').removeClass('active');
        $(this).parent().addClass('active');
        loadUsersType(typeId, '', userType);
    });*/
	
	$(document).on("click", '[id^="reallocatebtn-"]', function() {
		var e = $(this);
		var row = e[0].id.split('-')[1];
	   $.ajax({
			url: jsBaseUrl+"/location/reallocateform",
			beforeSend: function() {$('#loading').show();},
			data: {
				id: row
			},
			dataType: "json"
		}).done(function (data) {
			if (data.success) {					    
				$('#reallocate_current_location').html(data.locationname);
				$('#loaded-location-reallocate-content').html(data.html);
				$('#reallocateModal').modal('show');
				$('#loading').hide();
			}
		});		
	});
	
	loadLocations('');
	
	function deleteLocations(url)
	{
		if (url == "")
			url = jsBaseUrl + "/location/loaddeleted";		
		//
		$('#loading').show();
		$("#location-deleted-content").children().prop('disabled', true);
		//alert(url);
		$.ajax({
			url: url,			
			complete: function () {
				$('#location-deleted-content .pagination a, #location-deleted-content th a').each(function () {
					$(this).attr('onClick', 'loadLocations("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				});
				//Do whatever you want here              
				$('#loading').hide();
				$("#location-deleted-content").children().prop('disabled', false);
				//load tooltip
				$.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
				});
				//load popover
				$.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
				});
				//
				$(".revertLocation").click(function(){
					var href = $(this).attr('href');
					$.ajax({
						url: href,
						success: function(data){
							if(data){
								deleteLocations('');
								$('.total_delete_count').html(data);
								//
								new PNotify({
									title: 'Notifications',
									text: 'Location has been successfully reverted!',
									type: 'info',
									styling: "bootstrap3",
									opacity: 0.8,
									delay: 5000
								});					
							} 
						} 
					});
					return false;
				});	
			}
		}).done(function (data) {
			//alert(data.toSource());
			$("#location-deleted-content").html(data.html);
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#location-deleted-content table").stacktable({
                                myClass: 'table table-striped table-bordered'
                        });
                });                        
		});
	}
	
	function loadLocations(url)
	{
		if (url == "")
			url = jsBaseUrl + "/location/load";		
		//
		$('#loading').show();
		$("#location-loaded-content").children().prop('disabled', true);
		//alert(url);
		$.ajax({
			url: url,			
			complete: function () {
				$('#location-loaded-content .pagination a, #location-loaded-content th a').each(function () {
					$(this).attr('onClick', 'loadLocations("' + $(this).attr('href') + '");');
					$(this).attr('href', 'javascript:void(0);');
				});
				//Do whatever you want here              
				$('#loading').hide();
				$("#location-loaded-content").children().prop('disabled', false);
				//load tooltip
				$.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
				});
				//load popover
				$.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
				});
				//
				$(".deleteLocation").click(function(){
					var href = $(this).attr('href');
					$('#deleteConfirm').modal('show');
					$('#yes-delete-order').attr('href', href);
					return false;
				});
			}
		}).done(function (data) {
			//alert(data.toSource());
			$("#location-loaded-content").html(data.html);
                        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                            $("#location-loaded-content table").stacktable({
                                myClass: 'table table-striped table-bordered'
                        });
                });                        
		});
	}
	
	$(document).on("click touchstart", '#searchLocationBtn', function(event) { 
		if($('#searchLocation').val().length===0)
			alert('Search field value missing!');
		else{
			//hide list gridview
			$('#location-main-gridview').hide();
			//show search gridview
			$('#location-search-gridview').show();		
			//process order search 
			searchLocation($('#searchLocation').val(), '');	
		}
	});

	$(document).on("keyup", '#searchLocation', function(event) { 
		var inputContent = $(this).val();
		if(event.keyCode != 46) {
			if( (inputContent.length > 1)) {
				//hide list gridview
				$('#location-main-gridview').hide();
				//$("#inventory-loaded-content").html('');
				//show search gridview
				$('#location-search-gridview').show();	
				//alert(inputContent);
				//process inventory search 
				searchLocation(inputContent, '');	
			}
		}
	});
	
	$(document).on("keydown", '#searchLocation', function(event) { 
		if( (event.keyCode == 13)) {
			//hide list gridview
			$('#location-main-gridview').hide();
					
			//show search gridview
			$('#location-search-gridview').show();
					$('#myTabContent .tab-pane').removeClass('active');
					$('#myTabContent .order-all').parent().addClass('active');
			//process order search 
			searchLocation($(this).val(), '');	
			//
			event.preventDefault();
			return false;
		}
	});	
	
function searchLocation(query, url)
{
    $('.mobile-menu').hide();
	var _url;
	if(url != ""){
            _url = url;
        } 
        else{
            _url = jsBaseUrl+"/location/search?query="+query;
        }
	$.ajax({
		url: _url,
		dataType: "json",
		beforeSend: function() { $('#loading').show(); $("#location-loaded-content-search").children().prop('disabled',true);},
		complete: function() { 
			$('#loading').hide(); 
			$("#location-loaded-content-search").children().prop('disabled',false);
			//pagination 
			$("#location-loaded-content-search .pagination a").each(function() {
				$(this).attr('onClick', 'searchLocation("","' + $(this).attr('href') + '");');
				$(this).attr('href', 'javascript:void(0);');
			});			
			//sorting
			$("#location-loaded-content-search thead th a").each(function() {
				$(this).attr('onClick', 'searchLocation("","' + $(this).attr('href') + '");');
				$(this).attr('href', 'javascript:void(0);');		
			}); 				
		}
	}).done(function (data) {
            $('#ordersearch').addClass('active');
		//alert(data);
		if (data.success) {
			$("#location-results-count").html(data.count);
			$("#location-loaded-content-search").html(data.html);
			$.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
				$("#location-loaded-content-search").stacktable({
					myClass: 'table table-striped table-bordered'
				});
			});			
		}
	});	
	//e.preventDefault();
}