$(document).ready(function () {
    $("#submitForm").validate({
        validClass: "success",
        rules: {
            name:{
                required: true
            },
            t_number:{
                required: true,
                digits: true
            },
        },
        messages: {
            name:{
                required: langs('messages.field_required'),
            },
            t_number:{
                required: langs('messages.field_required'),
                digits: langs('messages.field_digits'),
            }
        },
        highlight: function(element) {
            $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
        },
        success: function(element) {
            $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
        },
    });
})
