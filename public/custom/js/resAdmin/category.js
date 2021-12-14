$(document).ready(function () {
    $("#submitForm").validate({
        validClass: "success",
        rules: {
            name:{
                required: true
            }
        },
        messages: {
            name:{
                required: langs('messages.field_required'),
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
