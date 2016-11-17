window.onload = function () {
    loadServiceOpenReport('');
    loadServiceCloseReport('');
    loadReallocationReport('', '');
    loadDivisionReport('');
    $("#service-report-content table, #reallocation-report-content table, #serviceclose-report-content table, [id^='loaded-content-location'] table,  [id^='loaded-content-category'] table").stacktable({
            myClass: 'table table-striped table-bordered'
    });
    $("#service-report-content .pagination a, #service-report-content th a").each(function () {
        $(this).attr('onClick', 'searchReport("' + $(this).attr('href') + '", "service", "");');
        $(this).attr('href', 'javascript:void(0);');
    });
    $("#reallocation-report-content .pagination a, #reallocation-report-content th a").each(function () {
        $(this).attr('onClick', 'searchReport("' + $(this).attr('href') + '", "reallocation", "");');
        $(this).attr('href', 'javascript:void(0);');
    });
    $("#serviceclose-report-content .pagination a, #serviceclose-report-content th a").each(function () {
        $(this).attr('onClick', 'searchReport("' + $(this).attr('href') + '", "serviceclose", "");');
        $(this).attr('href', 'javascript:void(0);');
    });
    $('#reportrange').daterangepicker({
        endDate: moment().format('MM-DD-YYYY'),
        startDate: moment().subtract(1, 'months').format('MM-DD-YYYY'),
        endDate: moment(),
                minDate: '01/01/2012',
        maxDate: moment().format('MM-DD-YYYY'),
        calender_style: "picker_2"
    });
    $('#reportrange').on('apply.daterangepicker', function (ev, picker) {
        $('#reportrange span').html(picker.startDate.format('MMMM D, YYYY') + " - " + picker.endDate.format('MMMM D, YYYY'));
        loadReallocationReport("", picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD'));
        var excelHref = $('.reallocation_excel_link').attr('href');
        var pdfHref = $('.reallocation_pdf_link').attr('href');;
        if(excelHref.indexOf('daterange') < 0){
            excelHref +='&daterange=' + picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD');
            pdfHref +='&daterange=' + picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD');
        } else {
            var excelHref = excelHref.split('&').slice(0);
            excelHref = excelHref[0] + '&daterange=' + picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD');
            var pdfHref = pdfHref.split('&').slice(0);
            pdfHref = pdfHref[0] + '&daterange=' + picker.startDate.format('YYYY-MM-DD') + "|" + picker.endDate.format('YYYY-MM-DD');            
        }
        $('.reallocation_excel_link').attr('href', excelHref);
        $('.reallocation_pdf_link').attr('href', pdfHref);
    });
    $(document).on('click', '[id^="load-models-location-"]', function(){
        var lid = $(this).attr('lid');
        $(this).toggle();
        $('#close-models-location-'+lid).toggle();
        $('#loaded-content-location-'+lid).toggle();
    });
    $(document).on('click', '[id^="close-models-location-"]', function(){
        var lid = $(this).attr('lid');
        $(this).toggle();
        $('#load-models-location-'+lid).toggle();
        $('#loaded-content-location-'+lid).toggle();
    });
}

function loadServiceOpenReport(url){
    if (url === '') {
        url = jsBaseUrl + "/analytics/getserviceopenreport";
    }
    $.ajax({
        url: url,
        data: {division_id: $('#openservice-divisionId').val()},
        dataType: 'JSON',
        beforeSend: function(){
          $('#openservice-loading').show();  
        },
        success: function(data){
            $('#service-report-content').html(data.html).removeClass('min-height-100');
            $("#service-report-content .pagination a, #service-report-content th a").each(function () {
                $(this).attr('onClick', 'loadServiceOpenReport("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        },
        complete: function(){
            $('#openservice-loading').hide();
            $("#service-report-content table").stacktable({
                    myClass: 'table table-striped table-bordered'
            });
        }
    });
}
function loadServiceCloseReport(url){
    if (url === '') {
        url = jsBaseUrl + "/analytics/getserviceclosereport";
    }
    $.ajax({
        url: url,
       data: {division_id: $('#closeservice-divisionId').val()}, 
        dataType: 'JSON',
        beforeSend: function(){
          $("#closeservice-loading").show();  
        },
        success: function(data){
            $("#serviceclose-report-content").html(data.html).removeClass('min-height-100');
            $("#serviceclose-report-content .pagination a, #serviceclose-report-content th a").each(function () {
                $(this).attr('onClick', 'loadServiceCloseReport("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        },
        complete: function(){
            $("#closeservice-loading").hide();
            $("#serviceclose-report-content table").stacktable({
                    myClass: 'table table-striped table-bordered'
            });
        }
    });
}
function loadReallocationReport(url, daterange){
    var data = {};
    if (url === '') {
        url = jsBaseUrl + "/analytics/getreallocationreport";
    }
    if (daterange != "") {
        data = {daterange: daterange, division_id: $('#reallocation-divisionId').val()};
    } else {
        data = {division_id: $('#reallocation-divisionId').val()};
    }
    $.ajax({
        url: url,
        data: data,
        dataType: 'JSON',
        beforeSend: function(){
          $("#reallocation-loading").show();  
        },
        success: function(data){
            $("#reallocation-report-content").html(data.html).removeClass('min-height-100');
            $("#reallocation-report-content .pagination a, #reallocation-report-content th a").each(function () {
                $(this).attr('onClick', 'loadReallocationReport("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        },
        complete: function(){
            $("#reallocation-loading").hide();
            $("#reallocation-report-content table").stacktable({
                    myClass: 'table table-striped table-bordered'
            });
        }
    });
}
function loadDivisionReport(url){
    if (url === '') {
        url = jsBaseUrl + "/analytics/getdivisionreport";
    }
    $.ajax({
        url: url,
        dataType: 'JSON',
        data : {division_id: $('#division-divisionId').val()},
        beforeSend: function(){
          $("#division-loading").show();  
        },
        success: function(data){
            $("#division-report-content").html(data.html).removeClass('min-height-100');
            $("#division-report-content .pagination a, #division-report-content th a").each(function () {
                $(this).attr('onClick', 'loadDivisionReport("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
        },
        complete: function(){
            $("#division-loading").hide();
            $("#division-report-content table").stacktable({
                    myClass: 'table table-striped table-bordered'
            });
        }
    });
}
function searchReport(url, type, daterange) {
    var _url = url;
    var selector = $('#' + type + '-report-content');
    if (url == '') {
        var _url = jsBaseUrl + "/analytics/index?report=" + type;
    } else {
        if (_url.indexOf('report') < 0) {
            _url += '&report=' + type;
        }
    }
    if (daterange != "") {
        if (_url.indexOf('daterange') < 0) {
            _url += '&daterange=' + daterange;
        }
    }
    //alert(_url);
    $.ajax({
        url: _url,
        dataType: "json",
        beforeSend: function () {
            $('#loading').show();
        },
        success: function (data) {
            if (data.success) {
                $('#loading').hide();
                selector.html(data.html);
            }
        },
        complete: function (data) {
            selector.find(".pagination a, th a").each(function () {
                $(this).attr('onClick', 'searchReport("' + $(this).attr('href') + '", "' + type + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            selector.find('.pagination a').click(function () {
                return false;
            });
            selector.find('table').stacktable({
                myClass: 'table table-striped table-bordered'
            });
        }
    });
}