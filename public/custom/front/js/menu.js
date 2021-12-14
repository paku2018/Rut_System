$(document).ready(function () {
    checkCount()
})
$('.minus-btn').on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $input = $this.closest('div').find('input');
    var value = parseInt($input.val());

    if (value > 1) {
        value = value - 1;
    } else {
        value = 0;
    }

    $input.val(value);
    checkCount()
});

$('.plus-btn').on('click', function(e) {
    e.preventDefault();
    var $this = $(this);
    var $input = $this.closest('div').find('input');
    var value = parseInt($input.val());

    value = value + 1;

    $input.val(value);
    checkCount()
});

function checkCount(){
    let count = 0;
    $('.order_count').each(function () {
        let val = $(this).val();
        if(val > 0){
            count ++;
        }
    })
    if (count > 0){
        $('.btn-order').prop('disabled', false)
    }else{
        $('.btn-order').prop('disabled', true)
    }
}

$(document).on('click','.btn-order', function () {
    let logged = $('#logged-status').val();
    if(logged == 0){
        location.href = "/login"
    }else{
        let items = [];
        $('.order_count').each(function () {
            let id = $(this).data('value');
            let val = $(this).val();
            if(val > 0){
                items[id] = val;
            }
        })
        let formData = new FormData();
        formData.append('items',JSON.stringify(items));
        formData.append('_token',_token);
        showLoading()
        $.ajax({
            url: path_order,
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                hideLoading()
                if(response.result){
                    if(response.success){
                        swal(langs('messages.success'), {
                            icon: "success",
                            buttons : {
                                confirm : {
                                    className: 'btn btn-success'
                                }
                            }
                        }).then((confirmed) => {
                            if (confirmed) {
                                location.reload();
                            }
                        });
                    }
                    else{
                        swal(langs('messages.server_error'), {
                            icon: "error",
                            buttons : {
                                confirm : {
                                    className: 'btn btn-danger'
                                }
                            }
                        });
                    }
                }else{
                    swal(langs('messages.server_error'), {
                        icon: "error",
                        buttons : {
                            confirm : {
                                className: 'btn btn-danger'
                            }
                        }
                    });
                }
            },
        });
    }
})
