$(document).ready(function () {
	$('#single_cal1').daterangepicker({
		singleDatePicker: true,
		calender_style: "picker_1"
	}, function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
	});
	$('#single_cal2').daterangepicker({
		singleDatePicker: true,
		calender_style: "picker_2"
	}, function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
	});
	$('#single_cal3').daterangepicker({
		singleDatePicker: true,
		calender_style: "picker_3"
	}, function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
	});
	$('#single_cal4').daterangepicker({
		singleDatePicker: true,
		calender_style: "picker_4"
	}, function (start, end, label) {
		console.log(start.toISOString(), end.toISOString(), label);
	});
});