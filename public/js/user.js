function loadUser(url)
{
    if (url == ""){
        url = jsBaseUrl + "/users/load";
        $("#user-loaded-content").html('');
        $("#userhome .x_title span").html('All');
        $('#myTabContent .tab-pane').removeClass('active');
        $('#userhome').addClass('active in');
    }
    $('#loading').show();
    $("#user-loaded-content").children().prop('disabled', true);
    $.ajax({
        url: url,
        //dataType: "json",
        //encode          : true,			
        complete: function () {
            //
            //$("#user-loaded-content").find('script').remove();
            $('#user-loaded-content .pagination a, #user-loaded-content th a').each(function () {
                $(this).attr('onClick', 'loadUser("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //Do whatever you want here              
            $('#loading').hide();
            $("#user-loaded-content").children().prop('disabled', false);
            //load tooltip
            $.getScript(jsBaseUrl + "/public/js/tooltip.js", function (data, textStatus, jqxhr) {
            });
            //load popover
            $.getScript(jsBaseUrl + "/public/js/popover.js", function (data, textStatus, jqxhr) {
            });
        }
    }).done(function (data) {
        //alert(data.toSource());
        $("#user-loaded-content").html(data.html);
        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
            $("#user-loaded-content").stacktable({
                    myClass: 'table table-striped table-bordered'
            });
        });
    });
}
//
function loadSearchUser(url)
{
    $('.mobile-menu').hide();
    $.ajax({
        url: url,
        dataType: "json",
        beforeSend: function () {
            $('#loading').show();
            $("#user-loaded-content-search").children().prop('disabled', true);
        },
        complete: function () {
            //pagination 
            $('#user-loaded-content-search .pagination a').each(function () {
                $(this).attr('onClick', 'loadSearchUser("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //sorting
            $('#user-loaded-content-search thead th a').each(function () {
                $(this).attr('onClick', 'loadSearchUser("' + $(this).attr('href') + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //
            $('#loading').hide();
            $("#user-loaded-content-search").children().prop('disabled', false);
        }
    }).done(function (data) {
        //alert(data);
        if (data.success) {
            $("#user-results-count").html(data.count);
            $("#user-loaded-content-search").html(data.html);
            $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                $("#user-loaded-content-search").stacktable({
                        myClass: 'table table-striped table-bordered'
                });
            });
        }
    });
    //e.preventDefault();
}

function deleteUsers(url) {
    var __url = url;
    if (__url == "")
        __url = jsBaseUrl + "/users/getdeleted";
    $.ajax({
        url: __url,
        beforeSend: function () {
            $('#loading').show();
            $('#userdelete').hide()
        },
        dataType: "json",
        complete: function () {
            //$("#order-loaded-content-"+type).find('script').remove();
            //
            var hideAllPopovers = function () {
                $('.popup-marker').each(function () {
                    $(this).popover('destroy');
                });
            };
            //Do whatever you want here              
            $('#loading').hide();
            //pagination 
            $("#user-deleted-content .pagination a").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', 'javascript:void(0);');
                $(this).on("click touchstart", function (event) {
                    deleteUsers($(this).data('href'));
                    event.preventDefault();
                    return false;
                });
            });
            $(".revertUser, .qrevertUser").click(function () {
                var href = $(this).attr('href');
                $.ajax({
                    url: href,
                    success: function (data) {
                        if (data) {
                            deleteUsers('');
                            $('.total_delete_count').html(data);
                        }
                    }
                });
                return false;
            });
            $(".deleteUser").click(function () {
                var href = $(this).attr('href');
                $('#deleteConfirm').modal('show');
                $('#yes-delete-order').attr('href', href);
                return false;
            });
            //sorting
            $("#user-deleted-content").each(function () {
                $(this).attr('data-href', $(this).attr('href'));
                $(this).attr('href', 'javascript:void(0);');
                $(this).on("click touchstart", function (event) {
                    deleteUsers($(this).data('href'), customerid);
                    event.preventDefault();
                    return false;
                });
            });

            $('#myTabContent .tab-pane').removeClass('active');
            $('#myTabContent #userdelete').addClass('active in');
        }
    }).done(function (data) {
        if (data.success) {
            $("#user-deleted-content").html(data.html);
            $('.total_delete_count').html(data.total);
            $('#userdelete').show();
            $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                $("#user-deleted-content").stacktable({
                        myClass: 'table table-striped table-bordered'
                });
            })
        }
    });
}

$('document').ready(function () {
    $.getScript("http://netdna.bootstrapcdn.com/twitter-bootstrap/2.3.1/js/bootstrap.min.js", function (data, textStatus, jqxhr) {
    });
    $('table').stacktable();
    $(document).on('click', '.loadUsersDepartment', function () {
        var depId = $(this).attr('uid');
        var department = $(this).html().trim();
        $('#myTab ul li').removeClass('active');
        $(this).parent().addClass('active');
        loadUsersDepartment(depId, '', department);
    });
    $(document).on('click', '.loadUsersType', function () {
        var typeId = $(this).attr('uid');
        var userType = $(this).html().trim();
        $('#myTab ul li').removeClass('active');
        $(this).parent().addClass('active');
        loadUsersType(typeId, '', userType);
    });
    $(document).on('click', '.loadUsersProjects', function () {
        var project_id = $(this).attr('project_id');
        var project = $(this).html().trim();
        $('#myTab ul li').removeClass('active');
        $(this).parent().addClass('active');
        loadUsersProjects(project_id, '', project);
    });
    $(document).on('click', '.viewUser', function () {
        var userId = $(this).attr('uid');
        loadUserDetails(userId);
    });
    $(document).on('click', ".deleteUser", function () {
        var delHref = $(this).attr('href');
        $('#deleteConfirm').modal('show');
        $('#yes-delete-order').attr('href', delHref);
        return false;
    });

    $(".createUser").on('click', function () {
        loadUserCreationForm();
    });

    $(document).on('click', ".updateUser", function () {
        var userId = $(this).attr('uid');
        loadUserUpdateForm(userId);
    });

    $(".showDepartments").on('click', function () {

        loadAllDepartments();
    });

    $(document).on('click', '.addNewDepartment', function (e) {
        $("#departmentsPops").modal('hide');
        loadDepartmentCreationForm();
    });

    $(document).on('click', '.editDepartment', function (e) {

        var dId = $(this).attr('did');
        $("#departmentsPops").modal('hide');
        loadDepartmentEditForm(dId);

    });

    $(document).on('change', '#u_usertype', function (e) {

        var fd = $(this).val();


        if (fd == 3) {

            $("#veryDepartment").css('display', 'block');

        } else {

            $("#veryDepartment").css('display', 'none');

        }

    });

    function loadDepartmentEditForm(dId) {

        $.ajax({
            url: jsBaseUrl + "/users/default/editdepartment",
            data: {
                id: dId
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {

                $("#departmentCreationFormViewUpdate").html(data.html);
                $("#depatmentCreationsPopsUpdate").modal('show');

                $("#departmentCreationFormUpdate").validate({
                    rules: {
                        departmentName: "required"
                    },
                    messages: {
                        departmentName: "Department name is required."
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
    function loadDepartmentCreationForm() {

        $.ajax({
            url: jsBaseUrl + "/users/default/createdepartment",
            data: {
                id: 0
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {

                $("#departmentCreationFormView").html(data.html);
                $("#depatmentCreationsPops").modal('show');

                $("#departmentCreationForm").validate({
                    rules: {
                        departmentName: "required"
                    },
                    messages: {
                        departmentName: "Department name is required."
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
    function loadAllDepartments(id) {
        $.ajax({
            url: jsBaseUrl + "/users/default/showdepartments",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#departmentsPopsView").html(data.html);
                $("#departmentsPops").modal('show');
            }
        });
    }

    jQuery.validator.addMethod("passmethod", function (value, element) {
        if (value.length == 0)
            return true;
        if (value.length > 0 && value.length < 6)
            return false;
        else
            return true;
    }, "Password length must be at least 6.");

    function loadUserUpdateForm(id) {
        $.ajax({
            url: jsBaseUrl + "/users/default/update",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#userUpdateForm").html(data.html);
                $("#userUpdate").modal('show');


                $("#userUpdateRegisterForm").validate({
                    rules: {
                        u_email: {
                            required: true,
                            email: true
                        },
                        u_firstname: "required",
                        u_lastname: "required",
                        u_username: "required",
                        u_hash_password: {
                            passmethod: true

                        },
                        u_usertype: {
                            required: true,
                            min: 1
                        }
                    },
                    messages: {
                        u_email: "Email is required.",
                        u_firstname: "First Name is required.",
                        u_lastname: "Last Name is required.",
                        u_username: "Username is required.",
                        u_usertype: "User Type is required.",
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });



            }
        });
    }
    function loadUserCreationForm() {
        $.ajax({
            url: jsBaseUrl + "/users/default/create",
            data: {
                id: 0
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#userCreationForm").html(data.html);
                $("#userCreation").modal('show');


                $("#userRegisterForm").validate({
                    rules: {
                        u_email: {
                            required: true,
                            email: true
                        },
                        u_firstname: "required",
                        u_lastname: "required",
                        u_username: "required",
                        u_hash_password: {
                            required: true,
                            minlength: 6,
                        },
                        u_usertype: {
                            required: true,
                            min: 1
                        }
                    },
                    messages: {
                        u_email: "Email is required.",
                        u_firstname: "First Name is required.",
                        u_lastname: "Last Name is required.",
                        u_username: "Username is required.",
                        u_hash_password: {
                            required: 'Password is required!',
                            minlength: 'Password length must be at least 6.',
                        },
                        u_usertype: "User Type is required.",
                    },
                    submitHandler: function (form) {
                        form.submit();
                    }
                });



            }
        });
    }


    function loadUserDetails(id) {
        $.ajax({
            url: jsBaseUrl + "/users/default/view",
            data: {
                id: id
            },
            dataType: "json"
        }).done(function (data) {
            if (data.success) {
                $("#detaisOfUser").html(data.html);
                $("#userDetails").modal('show');
            }
        });
    }

//	//pagination fix
//	$(document).on('click', '#user-main-gridview .pagination a', function() {
//		loadUser($(this).attr('data-href'));
//		return false;
//	});
//	//sort fix 
//	$(document).on('click', '#user-main-gridview thead a', function() {
//		loadUser($(this).attr('data-href'));
//		return false;
//	});	
    //
    $(document).on("click touchstart", '#searchUserBtn', function (event) {
        if ($('#searchUser').val().length === 0)
            alert('Search field value missing!');
        else {
            //hide list gridview
            $('#user-main-gridview').hide();
            $("#user-loaded-content").html('');
            //show search gridview
            $('#user-search-gridview').show();
            //process order search 
            searchUser($('#searchUser').val());
        }
    });

    loadUser("");

    $(document).on("keyup", '#searchUser', function (event) {
        var inputContent = $(this).val();
        if (event.keyCode != 46) {
            if ((inputContent.length > 1)) {
                //hide list gridview
                $('#user-main-gridview').hide();
                $("#user-loaded-content").html('');
                //show search gridview
                $('#user-search-gridview').show();
                //alert(inputContent);
                //process user search 
                searchUser(inputContent);
            }
        }
        else if (inputContent == "")
            loadUser("");
        else
            event.preventDefault();
    });
    //
    $(document).on("keydown", '#searchUser', function (event) {
        if ((event.keyCode == 13)) {
            //hide list gridview
            $('#user-main-gridview').hide();
            $("#user-loaded-content").html('');
            //show search gridview
            $('#user-search-gridview').show();
            //process user search 
            searchUser($(this).val());
            //
            event.preventDefault();
            return false;
        }
    });

    function searchUser(query)
    {
        $('.mobile-menu').hide();
        $.ajax({
            url: jsBaseUrl + "/users/search",
            data: {
                query: query
            },
            dataType: "json",
            beforeSend: function () {
                $('#loading').show();
                $("#user-loaded-content-search").children().prop('disabled', true);
                $('#user-search-gridview #myTab li, #usersearch').removeClass('active');
            },
            complete: function () {
                //pagination 
                $('#user-loaded-content-search .pagination a').each(function () {
//                    if ($(this).attr('href') != '#')
                        $(this).attr('onClick', 'loadSearchUser("' + $(this).attr('href') + '");');
                    $(this).attr('href', 'javascript:void(0);');
                });
                //sorting
                $('#user-loaded-content-search thead th a').each(function () {
                    $(this).attr('onClick', 'loadSearchUser("' + $(this).attr('href') + '");');
                    $(this).attr('href', 'javascript:void(0);');
                });
                //
                $('#loading').hide();
                $("#user-loaded-content-search").children().prop('disabled', false);
            }
        }).done(function (data) {
            //alert(data);
            if (data.success) {
                $("#user-results-count").html(data.count);
                $("#user-loaded-content-search").html(data.html);
                $('#user-search-gridview #myTab li, #usersearch').addClass('active');
                $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#user-loaded-content-search").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
            }
        });
        //e.preventDefault();
    }
});

function loadUsersDepartment(dep, url, department)
{
    if (url == "") {
        url = jsBaseUrl + "/departments/loadusers?dep=" + dep;
        $("#user-loaded-content").html('');
        $("#userhome .x_title span").html('Department: ' + department);
        $('#myTabContent .tab-pane').removeClass('active');
        $('#userhome').addClass('active in');
    }
    $('#loading').show();
    $("#user-loaded-content").children().prop('disabled', true);
    $.ajax({
        url: url,
        complete: function () {
            //
            //pagination  & sorting
            $('#user-loaded-content .pagination a, #user-loaded-content th a').each(function () {
                $(this).attr('onClick', 'loadUsersDepartment(' + dep + ',"' + $(this).attr('href') + '","' + department + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            $('#loading').hide();
            $("#user-loaded-content").children().prop('disabled', false);
        }
    }).done(function (data) {
        $("#user-loaded-content").html(data);
        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#user-loaded-content").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
    });
}
function loadUsersType(idtype, url, usertype)
{
    if (url == "") {
        url = jsBaseUrl + "/users/tload?type=" + idtype;
        $("#user-loaded-content").html('');
        $("#userhome .x_title span").html('Type: ' + usertype);
        $('#myTabContent .tab-pane').removeClass('active');
        $('#userhome').addClass('active in');
    }
    $('#loading').show();
    $("#user-loaded-content").children().prop('disabled', true);
    $.ajax({
        url: url,
        complete: function () {
            //
            //sorting & pagination 
            $('#user-loaded-content .pagination a, #user-loaded-content th a').each(function () {
                $(this).attr('onClick', 'loadUsersType(' + idtype + ',"' + $(this).attr('href') + '","' + usertype + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //             
            $('#loading').hide();
            $("#user-loaded-content").children().prop('disabled', false);
        }
    }).done(function (data) {
        $("#user-loaded-content").html(data);
        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#user-loaded-content").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
    });
}
function loadUsersProjects(project_id, url, project)
{
    if (url == "") {
        url = jsBaseUrl + "/users/loaduser?id=" + project_id;
        $("#user-loaded-content").html('');
        $("#userhome .x_title span").html('Project: ' + project);
        $('#myTabContent .tab-pane').removeClass('active');
        $('#userhome').addClass('active in');
    }
    $('#loading').show();
    $("#user-loaded-content").children().prop('disabled', true);
    $.ajax({
        url: url,
        complete: function () {
            //sorting & pagination 
            $('#user-loaded-content .pagination a, #user-loaded-content th a').each(function () {
                $(this).attr('onClick', 'loadUsersProjects(' + project_id + ',"' + $(this).attr('href') + '","' + project + '");');
                $(this).attr('href', 'javascript:void(0);');
            });
            //             
            $('#loading').hide();
            $("#user-loaded-content").children().prop('disabled', false);
        }
    }).done(function (data) {
        $("#user-loaded-content").html(data);
        $.getScript(jsBaseUrl+"/public/js/stacktable.js", function( data, textStatus, jqxhr ) {
                    $("#user-loaded-content").stacktable({
                            myClass: 'table table-striped table-bordered'
                    });
                });
    });
}