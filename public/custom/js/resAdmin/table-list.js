var tableId = 0;
$(document).on('click','.delete', function () {
    let id = $(this).data('index');
    swal({
        title: langs('messages.sure_delete'),
        text: langs('messages.table_will_delete'),
        type: 'question',
        icon: 'warning',
        buttons:{
            confirm: {
                text : langs('messages.yes'),
                className : 'btn btn-black'
            },
            cancel: {
                visible: true,
                text : langs('messages.cancel'),
                className: 'btn btn-custom-error'
            }
        }
    }).then((confirmed) => {
        if (confirmed) {
            let formData = new FormData();
            formData.append('id',id);
            formData.append('_token',_token);
            $.ajax({
                url: path_delete,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    if(response.result){
                        location.reload();
                    }else{
                        swal(langs('messages.delete_failed'), {
                            icon: "error",
                            buttons : {
                                confirm : {
                                    className: 'btn btn-custom-error'
                                }
                            }
                        });
                    }
                },
            });
        }
    });
})

$(document).on('click','.table-box', function () {
    tableId = $(this).data('index');
    let formData = new FormData();
    formData.append('_token',_token);
    formData.append('tableId',tableId);
    showLoading()
    $.ajax({
        url: path_table_info,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            hideLoading()
            if(response.result){
                let data = response.data;
                if(data.status == "closed"){
                    swal(langs('messages.no_orders_yet'), {
                        icon: "info",
                        buttons : {
                            confirm : {
                                className: 'btn btn-black'
                            }
                        }
                    })
                }else if(data.status == "open" || data.status == "pend"){
                    let orders = response.orders;
                    let code = '';
                    let total = 0;
                    for (let i=0; i<orders.length; i++){
                        let val = orders[i].product.sale_price * orders[i].order_count;
                        code += ' <div class="d-flex justify-content-between align-items-center mb-3"><div class="d-flex align-items-center">';
                        if (orders[i].product.image){
                            code += '<img class="order-image mr-2" src="' + orders[i].product.image + '">';
                        }
                        code += '<h4 class="text-danger mb-1">' + orders[i].product.name + '</h4></div>\n' +
                            '                                    <h4 class="mb-1">' + orders[i].product.sale_price + '*' + orders[i].order_count + '='+ val +'</h4>\n' +
                            '                                </div>'

                        total += val;
                    }
                    $('#assigned-orders').html(code);
                    $('#detail-total').html(total);
                    $('#detailModal').modal('show')
                }
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

$(document).on('click','.table-box .table-action', function (e) {
    e.stopPropagation();
})

$(document).on('click','.btn-confirm', function (e) {
    $('#detailModal').modal('hide')
    $('#tableId').val(tableId);
    $('#confirmModal').modal('show')
})

$("#confirmForm").validate({
    validClass: "success",
    rules: {
        consumption:{
            required: true
        },
        payment_method:{
            required: true
        },
        document_type:{
            required: true
        },
    },
    messages: {
        consumption:{
            required: langs('messages.field_required'),
        },
        payment_method:{
            required: langs('messages.field_required'),
        },
        document_type:{
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

$(document).on('click','.btn-close', function () {
    swal({
        title: langs('messages.sure_delete'),
        text: langs('messages.table_will_close'),
        type: 'question',
        icon: 'warning',
        buttons:{
            confirm: {
                text : langs('messages.yes'),
                className : 'btn btn-black'
            },
            cancel: {
                visible: true,
                text : langs('messages.cancel'),
                className: 'btn'
            }
        }
    }).then((confirmed) => {
        if (confirmed){
            showLoading()
            let formData = new FormData();
            formData.append('tableId',tableId);
            formData.append('_token',_token);
            $.ajax({
                url: path_close_table,
                type: 'post',
                data: formData,
                contentType: false,
                processData: false,
                success: function(response){
                    hideLoading()
                    if(response.result){
                        $('#detailModal').modal('hide');
                        swal(langs('messages.success'), {
                            icon: "success",
                            buttons : {
                                confirm : {
                                    className: 'btn btn-success'
                                }
                            }
                        }).then((confirmed) => {
                            location.reload();
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
        }
    });
})


