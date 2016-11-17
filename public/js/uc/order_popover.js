$(document).ready(function () {
	/*$('[id^="item-popover_"]').popover({
		trigger: 'manual',
		animation: false,
		html: true,
		title: function () {
			var current_id = $(this).attr('id').split("_").pop();
			var url = jsBaseUrl+"/ajaxrequest/getorderindexdetails?type=1&idorder=" + current_id;

			return $.ajax({url: url,
				dataType: 'html',
				async: false}).responseText;
		},
		content: function () {
			var current_id = $(this).attr('id').split("_").pop();
			var url = jsBaseUrl+"/ajaxrequest/getorderindexdetails?type=2&idorder=" + current_id;

			return $.ajax({url: url,
				dataType: 'html',
				async: false}).responseText;
		},
		container: 'body',
		placement: 'right'
	});*/
	$('[id^="item-popover_"]').hover(function() {
		$(this).popover('toggle');
	});	
});