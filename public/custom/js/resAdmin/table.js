var tableId = ""

$('#dt_table').DataTable({
    "pageLength": 10,
    "order": [[ 1, 'asc' ]],
    language: {
        url: path_lang_datatable
    }
});

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
    let email = $('#email').val();
    if(!email){
        $('.btn-order').prop('disabled', true)
        return
    }
    let new_total = 0;
    let count = 0;
    $('.order_count').each(function () {
        let val = $(this).val();
        if(val > 0){
            count ++;
            let price = $(this).data('price')
            new_total += val * price
        }
    })
    if (count > 0){
        $('.btn-order').prop('disabled', false)
    }else{
        $('.btn-order').prop('disabled', true)
    }
    $('#new-total').html(new_total)
}

$('#email').on('change', function(e) {
    checkCount()
});

$(document).on('click','.btn-order', function () {
    showLoading()
    let items = [];
    $('.order_count').each(function () {
        let id = $(this).data('value');
        let val = $(this).val();
        if(val > 0){
            items[id] = val;
        }
    })
    let email = $('#email').val();
    let name = $('#name').val();
    let formData = new FormData();
    formData.append('items',JSON.stringify(items));
    formData.append('email',email);
    formData.append('name',name);
    formData.append('_token',_token);
    $.ajax({
        url: path_create_delivery,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            hideLoading()
            if(response.result){
                swal(langs('messages.success'), {
                    icon: "success",
                    buttons : {
                        confirm : {
                            className: 'btn btn-success'
                        }
                    }
                }).then((confirmed) => {
                    $('.btn-print').attr('disabled', false)
                    tableId = response.data.id;
                });
            }else{
                swal(langs('messages.server_error'), {
                    icon: "error",
                    buttons : {
                        confirm : {
                            className: 'btn btn-danger'
                        }
                    }
                }).then((confirmed) => {
                    location.reload();
                });
            }
        },
    });
})

$(document).on('click', '.btn-print', function () {
    var url = HOST_URL + '/exportPdf/' + tableId

    $.ajax({
        url: url,
        type: 'get',
        contentType: false,
        processData: false,
        success: function(response){
            var url_png = HOST_URL+'/'+response.ticket_png;
            if(url_png.length>1){
                if(window.jspmWSStatus()){
                    doPrinting(url_png);
                }
                swal({
                    buttons : {
                        confirm : {
                            className: 'btn btn-danger'
                        }
                    },
                    icon: url_png,
                }).then((confirmed) => {
                    location.href = path_table
                });
            }

        },
        error: function(a){
            swal(langs('messages.server_error'), {
                icon: "error",
                buttons : {
                    confirm : {
                        className: 'btn btn-danger'
                    }
                }
            });
        }
    });


})

$(function () {
    checkCount()
});
