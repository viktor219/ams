var gCustomerId = 0;
$('document').ready(function () {
    $('table').stacktable();
    $(".deleteLocation").on('click', function () {
        if (!confirm("Are you sure to delete this location?"))
            return false;
        var customerId = $(this).attr('cid');
        var locationId = $(this).attr('lid');
        window.location = jsBaseUrl + "/customers/default/locationdelete?id=" + locationId+"&customer="+customerId;
    });

    $(".viewLocation").on('click', function () {

        var locationId = $(this).attr('lid')
        loadLocationDetails(locationId);
    });
    
    $(".createNewLocation").on('click', function () {
        gCustomerId = $(this).attr('presentcustomerid')
        loadLocationCreationForm();
    });
    
    $(".updateLocation").on('click', function () {

        var locationId = $(this).attr('lid')
        loadLocationUpdateForm(locationId);
    });

    
});

function loadLocationUpdateForm(id) {
        $.ajax({
            url: jsBaseUrl+"/customers/default/locationupdate",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#locationUpdateForm").html(data.html);
                $("#locationUpdate").modal('show');
                $("#locationUpdateRegisterForm").validate({
                    rules: {
                    location_address: "required",
                    location_city: "required",
                    location_country: "required",
                    location_state: "required",
                    location_zip: "required"
                    },
                    messages: {
                        location_address: "Address is required.",
                        location_city: "City is required.",
                        location_country: "Country is required.",
                        location_state: "State is required.",
                        location_zip: "Zip Code is required.",
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });
                
                
                
            }
        });
}

/**
 * 
 * @returns {undefined}
 */
function loadLocationCreationForm() {
    $.ajax({
        url: jsBaseUrl + "/customers/default/locationcreate",
        data: {
            id: 0,
            customer:gCustomerId
        },
        dataType: "json"
    }).done(function (data) {
        
        if (data.success) {
            
            $("#locationCreationForm").html(data.html);
            $("#locationCreation").modal('show');
            $("#locationRegisterForm").validate({
                rules: {
                location_address: "required",
                location_city: "required",
                location_country: "required",
                location_state: "required",
                location_zip: "required"
                },
                messages: {
                    location_address: "Address is required.",
                    location_city: "City is required.",
                    location_country: "Country is required.",
                    location_state: "State is required.",
                    location_zip: "Zip is required.",
                },
                submitHandler: function (form) {
                    form.submit();
                }
            });



        }
    });
}
/**
 * 
 * @param {type} id
 * @returns {undefined}
 */
function loadLocationDetails(id) {
    
    $.ajax({
        url: jsBaseUrl+"/customers/default/locationview",
        data: {
            id: id
        },
        dataType: "json"
    }).done(function (data) {
        if (data.success) {
            $("#detaisOfLocation").html(data.html);
            $("#locationDetails").modal('show');
        }
    });
}

