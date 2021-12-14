$("#submitForm").validate({
    validClass: "success",
    rules: {
        t_number:{
            required: true
        },
        name:{
            required: true
        }
    },
    messages: {
        t_number:{
            required: langs('messages.field_required'),
        },
        name:{
            required: langs('messages.field_required'),
        },
    },
    highlight: function(element) {
        $(element).closest('.form-group').removeClass('has-success').addClass('has-error');
    },
    success: function(element) {
        $(element).closest('.form-group').removeClass('has-error').addClass('has-success');
    },
});

$(function () {

});
