$(document).on("click touchstart", '#searchVendorBtn', function(event) { 
	if($('#searchVendor').val().length===0)
		alert('Search field value missing!');
	else{
		//show search gridview
		$('#vendor-search-tab').show();		
		//process order search 
		searchVendor($('#searchVendor').val(), '');	
	}
});
$(document).on("keyup", '#searchVendor', function(event) { 
	var inputContent = $(this).val();
	if(event.keyCode != 46) {
		if( (inputContent.length > 1)) {
			//show search gridview
			$('#vendor-search-tab').show();	
			//process inventory search 
			searchVendor(inputContent, '');	
		}
	}
});
//
$(document).on("keydown", '#searchVendor', function(event) { 
	if( (event.keyCode == 13)) {
		//show search gridview
		$('#vendor-search-tab').show();	
		//process order search 
		searchVendor($(this).val(), '');	
		//
		event.preventDefault();
		return false;
	}
});
//
function searchVendor(query, url)
{
	var _url;
	if(query=="")
		_url = url;
	else
		_url = jsBaseUrl+"/vendor/search?query="+query;
	//alert(_url);
	$.ajax({
		url: _url,
		dataType: "json",
		beforeSend: function() { $('#loading').show(); $("#vendor-loaded-content-search").children().prop('disabled',true);},
		complete: function() { 
			$('#myTab li').removeClass('active');
			$('#vendor-search-tab').addClass('active');
			$('#vendorhome').removeClass('active');
			$('#vendorsearch').addClass('active in');			
			//
			$('#loading').hide(); 
			$("#vendor-loaded-content-search").children().prop('disabled',false);
			//pagination 
			$("#vendor-loaded-content-search .pagination a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
			});	
			$(document).on('click', '#vendor-search-tab .pagination a', function() {
				searchVendor('', $(this).attr('data-href'));
				return false;
			});				
			//sorting
			$("#vendor-loaded-content-search thead th a").each(function() {
				$(this).attr('data-href', $(this).attr('href'));
				$(this).attr('href', '#');
				$(this).on("click touchstart", function(event) { 
					searchVendor('', $(this).attr('data-href'));
					event.preventDefault();
				});			
			}); 										
		}  
	}).done(function (data) {
		if (data.success) {			
			$("#vendor-results-count").html(data.count);
			$("#vendor-loaded-content-search").html(data.html);
		}
	});	
}