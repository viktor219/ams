$('[id^="load-models-category-"]').on('click', function() {
	var e = $(this);
	var categoryid = e.attr('cid');
	var customerid = e.attr('pid');
	//
	/*if($('#loaded-content-category-'+categoryid).text().length === 0)
	{
		$.ajax({
			url: jsBaseUrl+"/site/loadinstockpage?categoryid=" + categoryid + "&customerid=" + customerid,
			dataType: 'json',
			beforeSend: function() {$('#loading').show();},
			complete: function() {
				//hide pagination normal way getting...
				$('#loaded-content-category-' + categoryid + ' .pagination a').each(function() {
					if($(this).attr('href') != '#')
						$(this).attr('data-href', $(this).attr('href'));
					$(this).attr('href', 'javascript:;');
				}); 				
			},
			success: function(data) {
				if(data.success)
				{
					$('#loaded-content-category-' + categoryid).html(data.html);
					$('#loaded-content-category-' + categoryid).show("slow");
					e.hide();
					$('#close-models-category-' + categoryid).show();
					$('#loading').hide();
				}
			}
		});	
	}
	else 
	{*/
		$('#loaded-content-category-' + categoryid).show();
		e.hide();
		$('#close-models-category-' + categoryid).show();
	//}
}); 
//
$('[id^="close-models-category-"]').on('click', function() {
	var e = $(this);
	var categoryid = e.attr('cid');
	$('#loaded-content-category-' + categoryid).hide();
	$('#load-models-category-' + categoryid).show();
	e.hide();
});

function showAllModels()
{
	$('[id^="loaded-content-category-"]').show();
	$('[id^="close-models-category-"]').show();
	$('[id^="load-models-category-"]').hide();
}

function editCategory(modelid)
{
	$.ajax({
		url: jsBaseUrl+"/customers/loadeditcategoryform",
		data: {
			id: modelid,
		},
		dataType: "json",
		complete: function () {
			$('[name="category"]').select2({placeholder: "Select A Category", width: '100%', allowClear: true});
			$('[name="department"]').select2({placeholder: "Select A Department", width: '100%', allowClear: true});
		},
		encode          : true
	}).done(function (data) {
		if (data.success) {
			$("#mod_model_name").html(data.modelname);
			$("#update-loaded-content").html(data.html);
			$('#UpdateCategory').modal('show'); 
		}
	});	
}

$('form#update-category-form').validate({
	rules: {                     
		name: "required",
	},
	messages: {
		name: "Category name is required.",
	}
});
//
$('form#update-category-form').submit(function(event) {
	event.preventDefault(); // Prevent the form from submitting via the browser
	var $form = $(this);		
	$.ajax({
		type: 'POST',  
		url: jsBaseUrl+"/customers/updatemodeldata",
		data: $form.serialize(),
		dataType: "json",
		encode          : true								 
	}).done(function(data) {
		if(data.success) {
			$form[0].reset();
			$('#category-update-msg').html(data.html);
			$('#category-update-msg').show();							
			$("#category-update-msg").delay(2000).fadeOut("slow", function () { 
				$("#category-update-msg").hide(); 
				$("#department-"+$form.find('#modelId').val()).html(data.departmentname);
				$("#category-"+$form.find('#modelId').val()).html(data.categoryname);
				//$('#gridview-'+data.newcategoryid+' table tbody').appendTo($('#models-'+$form.find('#modelId').val()).text());
				var tr = $("#row-models-"+$form.find('#modelId').val()).remove().clone();
				$('#gridview-'+data.newcategoryid+' table tbody').append(tr);
				$('#UpdateCategory').modal('hide');
			});									
		}
	});										
});	
//
$('#viewByLocation').on('change', function () {
	//alert($(this).val());
	var currentUrl = window.location.href; 
	var locationid = $(this).val();
	var id = getUrlParameter('id');
	var newUrl = "http://"+window.location.host + jsBaseUrl + "/customers/ownstockpage?id="+id+"&location="+ locationid;
	//alert(newUrl);
	if (currentUrl!=newUrl) 
		window.location.replace(newUrl);
	//else 
	//	window.location.reload();
});
//
$('#rviewByLocation').on('change', function () {
	var currentUrl = window.location.href; 
	var locationid = $(this).val();
	var newUrl = "http://"+window.location.host + jsBaseUrl + "/overview/index?location="+ locationid;
	if (currentUrl!=newUrl) 
		window.location.replace(newUrl);
});