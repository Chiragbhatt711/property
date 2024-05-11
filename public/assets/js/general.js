function checkForInput(element) {
    // element is passed to the function ^

    const $label = $(element).siblings("label");

    if ($(element).val().length > 0) {
        $label.addClass("input-has-value");
    } else {
        $label.removeClass("input-has-value");
    }
}

// The lines below are executed on page load
$("input.form-control").each(function() {
    checkForInput(this);
});

// The lines below (inside) are executed on change & keyup
$("input.form-control").on("change keyup", function() {
    checkForInput(this);
});

// dropbox
$(`#imageInput`).change(function(event) {
    let previewImg = $(`#show_Image`);
    let reader = new FileReader();
    reader.onload = function(e) {
        let url = e.target.result;
        $(previewImg).attr("src", url);
        previewImg.parent().css("background", "transparent");
        previewImg.show();
        previewImg.siblings(".drop_cont").hide();
    };
    reader.readAsDataURL(this.files[0]);
});
$(`#bgImageInput`).change(function(event) {
    let previewImg = $(`#bgImageShow`);
    let reader = new FileReader();
    reader.onload = function(e) {
        let url = e.target.result;
        $(previewImg).attr("src", url);
        previewImg.parent().css("background", "transparent");
        previewImg.show();
        previewImg.siblings(".drop_cont").hide();
    };
    reader.readAsDataURL(this.files[0]);
});

$(".menu-toggle").click(function(e) {
    e.preventDefault();
    $("#hassidebar").toggleClass("toggled");
});

$("#headerdesh .headuser_info .dropdown .dropdown-menu").on(
    "click",
    function(event) {
        event.stopPropagation();
    }
);

$(document).ready(function() {
    $(".datepicker").datepicker({
        format: "yyyy-mm-dd",
        // startDate: '0d',
        weekStart: 0,
        calendarWeeks: true,
        autoclose: true,
        todayHighlight: true,
        // rtl: true,
        orientation: "auto"
    });
    // togale();
});


$('#forgot_pass').click(function(){
    $('#emailError').html();
    let email = $('#email').val();
    if(email)
    {
        $.ajaxSetup({
            headers: {
                "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
            },
        });
        $.ajax({
            type: "POST",
            url: "/reset-password",
            data: {
                email: email
            },
            success: function(data) {
                if (data.success) {
                    // window.location = 'login';
                    console.log(data.success);
                    $(`#forget_pass`).modal("show");
                }
                if (data.error) {
                        $("#emailError").html(
                            '<span class="text-danger">' +
                            data.error +
                            "</span>"
                        );
                }
            },
        });
    }
    else
    {
        $('#userEmailError').html('<span class="text-danger">Plese enter Username or email..</span>');
    }
});

$("#passgenerate").click(function() {
    var password = Array(20).fill('0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz~!@-#$%^&*')
    .map(x => x[Math.floor(crypto.getRandomValues(new Uint32Array(1))[0] / (0xffffffff + 1) * x.length)]).join(
    '');
    $("#password").val(password);
});

function togale()
{
    var is_active = $('#is_active').val();
        if(is_active == 0)
        {
          $('#is_active').val('1');
        }
        else if(is_active == "on")
        {
          $('#is_active').val('1');
        }
        else
        {
          $('#is_active').val('0');
        }
}

function is_active_change(id,data)
{
    var is_active_change = $(data).val();
    if(is_active_change == 0)
    {
        $(data).val('1');
    }
    else if(is_active_change == "on")
    {
        $(data).val('1');
    }
    else
    {
        $(data).val('0');
    }
    var is_active =  $(data).val();
    var action = $(data).attr('data-action');
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: action,
        data: {
            id:id,
            is_active: is_active
        },
        success: function(data) {
            $('#successMsg').html('<span>'+data+'</span>');
        },
    });
}

function changeQuestionStatus(id,data)
{
    var is_active_change = $(data).val();
    if(is_active_change == 0)
    {
        $(data).val('1');
    }
    else if(is_active_change == "on")
    {
        $(data).val('1');
    }
    else
    {
        $(data).val('0');
    }
    var is_active =  $(data).val();
    var action = $(data).attr('data-action');
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: action,
        data: {
            id:id,
            is_active: is_active
        },
        success: function(data) {
            $('#successMsg').show();
            $('#successMsg').html('<span>'+data+'</span>');
            $('#question_status').prop('checked', false);
            $('#question').val('');
            $('#questionSubmit').show();
            $('#questionUpdate').hide();
        },
    });
}

$('#newsSubmit').click(function(){
    $('#titleError').html('');
    $('#descriptionError').html('');
    let title = $('#title').val();
    let description = $('#description').val();
    let error = 0;
    if(title == "")
    {
        $('#titleError').html('<span class="text-danger">Please enter title</span>');
        error = 1;
    }
    if(description == "")
    {
        $('#descriptionError').html('<span class="text-danger">Please enter description</span>');
        error = 1;
    }
    if(error != 0)
    {
        return false;
    }
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: "/ordergatway/news-post",
        data: {
            title:title,
            description:description
        },
        success: function(data) {
            $('#title').val('');
            $('#description').val('');
            $('#successMsg').html('<span>'+data.success+'</span>');
            var created_at = new Date(data.news.created_at);
            var table = document.getElementById('newsTBody');
            var row = table.insertRow(0);
            var cell0 = row.insertCell(0);
            var cell1 = row.insertCell(1);
            var cell2 = row.insertCell(2);
            var cell3 = row.insertCell(3);
            cell0.innerHTML = data.news.title;
            cell1.innerHTML = data.news.description;
            cell2.innerHTML = created_at.toISOString().slice(0, 10);
            cell3.innerHTML = `<a onclick="deletePopup('`+data.news.id+`')" class="download_btn">Delete</i></a>`;
        },
    });
});

$('#notification').click(function(){
    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: "/notification",
        data: {

        },
        success: function(data) {

            let notification = '';
            if(data.notification)
            {
                $.each(data.notification, function(key, value){
                    let img = `<img src="assets/images/porfile.png" alt="">`;
                    let title = value.title;
                    let description = value.description;
                    let created_at = new Date(value.created_at);

                    notification += '<div class="all_notify all_notify_popUp">'+
                                        '<div class="profile">'+
                                            img +
                                        '</div>'+
                                        '<div class="info">'+
                                            '<div class="title_desc">'+
                                               '<h4>'+ title +':'+ description + '</h4>'+
                                            '</div>'+
                                            '<div class="date">'+
                                                '<span>'+ created_at.toISOString().slice(0, 10)+ '</span>'+
                                            '</div>'+
                                        '</div>'+
                                    '</div>';
                })
            }
            $('#notificationBody').html(notification);
            $('#notificationCount').html(data.notification_count);
            $('#notificationModal').modal('show');
        },
    });
});


function statusChange(data){
    if (data.checked)
    {
        $(data).val(1);
    }
    else
    {
        $(data).val(0);
    }
}

$('#questionSubmit').click(function(){
    if(($('#flashSuccessMsg').length > 0))
    {
        $('#flashSuccessMsg').hide();
    }
    var question_name= $('#question').val()
    var status = $('#question_status').val();
    $('.form-error').html('');

    var action = $('#question-form').attr('action');
    var btn = $('#questionSubmit');
    btn.attr('data-temp',btn.html()).attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i>Please wait');

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: action,
        data: {
            question_name:question_name,
            status:status
        },
        success: function(response) {
            data = $.parseJSON(response);
            console.log(data)
            btn.removeAttr('disabled').html(btn.attr('data-temp'));
            if(data.status == 'success')
            {
                $('#question').val('');
                $('#question_status').val('');
                $('#question_status').prop('checked', false);
                $('#successMsg').html('<span>Question inserted successfully.</span>');
                // var created_at = new Date(data.news.created_at);
                var table = document.getElementById('questionTBody');
                var row = table.insertRow(0);
                var cell0 = row.insertCell(0);
                var cell1 = row.insertCell(1);
                var cell2 = row.insertCell(2);
                var status = 'No';
                if(data.question.status == 1)
                {
                    status = 'Yes';
                }
                cell0.innerHTML = data.question.question_name;
                cell1.innerHTML = status;
                cell2.innerHTML = `<a href="javascript:void(0)" data-id="`+data.question.id+`" onclick="questionEdit(`+data.question.id+`)" class="download_btn">Edit</i></a>
                                    <a href="javascript:void(0)" onclick="deletePopup('`+data.question.id+`')" class="download_btn">Delete</i></a>`;
            }
            else
            {
                $.each(data.errors, function(key, value) {
                    $.each(value, function(k, val) {
                        $('#error-'+k).html('<span class="text-danger">'+val+'</span>');
                    })
                })
            }
        },
    });

});

function questionEdit(id)
{
    $('#successMsg').hide();
    if(($('#flashSuccessMsg').length > 0))
    {
        $('#flashSuccessMsg').hide();
    }
    $('.form-error').html('');
    var action = $('#question-form').attr('edit-action');

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: action,
        data: { id:id },
        success: function(data) {

            $('#question_id').val(data.question.id);
            $('#question').val(data.question.question_name);
            $('#question_status').val(data.question.status);
            if(data.question.status == 1)
            {
                $('#question_status').prop('checked', true);
            }
            else
            {
                $('#question_status').prop('checked', false);
            }
            $('#questionSubmit').hide();
            $('#questionUpdate').show();
        },
    });
}

$('#question-form').submit(function(){
    if(($('#flashSuccessMsg').length > 0))
    {
        $('#flashSuccessMsg').hide();
    }
    return false;
});
$('#questionUpdate').click(function(){
    if(($('#flashSuccessMsg').length > 0))
    {
        $('#flashSuccessMsg').hide();
    }
    $('.form-error').html('');
    var btn = $('#questionUpdate');
    btn.attr('data-temp',btn.html()).attr('disabled','disabled').html('<i class="fa fa-spinner fa-spin"></i>Please wait');
    var action = $('#question-form').attr('update-action');
    var id = $('#question_id').val();
    var question_name= $('#question').val()
    var status = $('#question_status').val();

    $.ajaxSetup({
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
    });
    $.ajax({
        type: "POST",
        url: action,
        data: {
            id:id,
            question_name:question_name,
            status:status
        },
        success: function(response) {
            data = $.parseJSON(response);
            btn.removeAttr('disabled').html(btn.attr('data-temp'));

            if(data.status == 'success')
            {
                location.reload(true);
            }
            else
            {
                $.each(data.errors, function(key, value) {
                    $.each(value, function(k, val) {
                        $('#error-'+k).html('<span class="text-danger">'+val+'</span>');
                    })
                })
            }
        },
    });
});
