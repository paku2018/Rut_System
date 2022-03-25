var tableId = 0;
var nf = Intl.NumberFormat();

$(document).ready(function () {
    getTableList()
    setInterval(function () {
        getTableList()
    }, 10000);
})

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
                let orders = response.orders;

                generateOrderedList(data, orders)
                checkCount()
                checkDisable();
                $('#detailModal').modal('show')
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

function generateOrderedList(tableData, orders){
    let code = '';
    let total = 0;
    let comment = '';
    let comment_list = [];
    if (orders.length > 0){
        code += '<div class="w-100 d-flex justify-content-end mb-2">'
        if(tableData.status == "ordered")
            code += '<button class="btn btn-black btn-round btn-deliver btn-sm mr-2"><i class="fa fa-check mr-2"></i>' + langs('messages.mark_as_deliver') + '</button>'
        code += '<button class="btn btn-danger btn-round btn-order-delete btn-sm">' + langs('messages.delete') + '</button></div>'
        for (let i=0; i<orders.length; i++){
            let val = orders[i].product.sale_price * orders[i].order_count;
            let delivered = orders[i].deliver_status == 1 ? '(Entregado)' : ''
            let direct_order_class = orders[i].direct == null? 'text-danger':'text-purple'
            let sub_orders = orders[i].children
            let sub_code = ''
            if (sub_orders.length > 0) {
                for (let ii=0; ii<sub_orders.length; ii++) {
                    sub_code += '<div class="d-flex justify-content-between">' +
                        '<h4 class="mb-0 ml-3 text-danger">' + sub_orders[ii].detail.name +'</h4>' +
                        '<h4 class="mb-0 text-black">' + sub_orders[ii].detail.sale_price.toLocaleString('de-DE') +'</h4>' +
                        '</div>'
                    total += sub_orders[ii].detail.sale_price
                }
            }
            code += ' <div class="pb-1" style="border-bottom: 1px solid #eeeeee"><div class="d-flex align-items-center mb-1">\n' +
                '                                    <input type="checkbox" name="orders_'+orders[i].id+'" class="orders mr-2" data-value="'+ orders[i].id +'">' +
                '                                    <h4 class="mb-0 ' + direct_order_class + '">' + orders[i].product.name + delivered + '</h4></div>\n' +
                '                                    <h4 class="text-right mb-1">' + orders[i].product.sale_price.toLocaleString('de-DE') + '*' + orders[i].order_count + '='+ val.toLocaleString('de-DE') +'</h4>';
            code += sub_code
            code += '</div>'

            total += val;

            if (orders[i].comment) {
                if (!comment_list.includes(orders[i].comment)) {
                    comment_list.push(orders[i].comment)
                    comment += '<p class="mt-1 mb-0">' + orders[i].comment + '</p>'
                }
            }
        }
    }
    $('#assigned-orders').html(code);
    $('#assigned-orders').append(comment)
    $('#detail-total').html(total.toLocaleString('de-DE'));
}

function getOrderList(){
    let formData = new FormData();
    formData.append('_token',_token);
    formData.append('tableId',tableId);
    $.ajax({
        url: path_table_info,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            if(response.result){
                let data = response.data;
                let orders = response.orders;
                generateOrderedList(data, orders)
                $('.order_count').each(function () {
                    $(this).val(0);
                })
                $('#comment').val('')
                checkCount()
                checkDisable()
            }else{
                $('#assigned-orders').html(langs('messages.server_error'));
            }
        },
    });
}

function getTableList() {
    let formData = new FormData();
    formData.append('_token',_token);
    $.ajax({
        url: path_table_list,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            let code = ''
            if(response.result){
                let tables = response.data;
                let len = tables.length
                if (len > 0) {
                    for (let i=0; i<len; i++) {
                        let status = tables[i].status;
                        let className = ''
                        let title = ''
                        let t_url = HOST_URL + "/restaurant/tables/edit/" + tables[i].id
                        switch (status) {
                            case "open":
                                className = 'bg-success-gradient success-shadow'
                                title = langs('messages.open')
                                break
                            case "ordered":
                                className = 'bg-warning-gradient'
                                title = langs('messages.ordered')
                                break
                            case "pend":
                                className = 'bg-danger-gradient'
                                title = langs('messages.provisional_close')
                                break
                            case "closed":
                                className = 'bg-black'
                                title = langs('messages.available')
                                break
                            default:
                                break
                        }
                        code += '<div class="table-box" data-index="' + tables[i].id + '">'
                        code += '<div class="table-status ' + className + '" title="' + title + '"></div>'
                        code += '<h6 class="text-center mb-0">' + langs('messages.table') + '-' + tables[i].t_number + '</h6>'
                        code += '<h5 class="text-center" style="height: 80px">' + tables[i].name + '</h5>'
                        code += '<div class="table-action d-flex align-items-center justify-content-center">'
                        code += '<a href="' + t_url + '" class="text-black"><i class="fas fa-edit"></i></a>'
                        code += '<div class="ml-2 text-red delete" data-index="' + tables[i].id + '"><i class="fas fa-trash"></i></div>'
                        code += '</div></div>'
                    }
                }else {
                    code = '<h6 class="text-center">No hay mesas disponibles.</h6>'
                }
            }else{
                code = '<h6 class="text-center">No hay mesas disponibles.</h6>'
            }
            $('.table-group').html(code)
        },
    });
}

$(document).on('click','.btn-update', function (e) {
    getTableList()
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

    //sub orders
    $('.sub_order').each(function () {
        var checked = $(this).is(":checked")
        if (checked) {
            var s_price = $(this).data('price')
            new_total += s_price
        }
    })

    $('#new-total').html(new_total.toLocaleString('de-DE'))
}

$(document).on('change', '.sub_order', function () {
    checkCount()
})

$(document).on('click','.btn-order', function () {
    showLoading()
    let items = [];
    $('.order_count').each(function () {
        let id = $(this).data('value');
        let val = $(this).val();
        if(val > 0){
            //get sub orders
            let sub_orders = []
            $('input[name=sub_order_' + id + ']:checked').each(function () {
                sub_orders.push($(this).val())
            })
            let item = {id: id, quantity: val, sub_orders: sub_orders.join(",")}
            items.push(item)
        }
    })

    let comment = $('#comment').val();
    let formData = new FormData();
    formData.append('items',JSON.stringify(items));
    formData.append('tableId',tableId);
    formData.append('comment',comment);
    formData.append('_token',_token);
    $.ajax({
        url: path_create_orders,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            hideLoading()
            if(response.result){
                $('#newModal').modal('hide');
                swal(langs('messages.success'), {
                    icon: "success",
                    buttons : {
                        confirm : {
                            className: 'btn btn-success'
                        }
                    }
                }).then((confirmed) => {
                    getOrderList();
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

function checkDisable(){
    let checked_count = 0;
    $('.orders').each(function () {
        let index = $(this).data('value');
        let checked = $('input[name=orders_'+index+']:checked').val();
        if (checked){
            checked_count++;
        }
    })
    if (checked_count == 0){
        $('.btn-order-delete').prop('disabled', true)
        $('.btn-deliver').prop('disabled', true)
    }else{
        $('.btn-order-delete').prop('disabled', false)
        $('.btn-deliver').prop('disabled', false)
    }
}

$(document).on('click','.table-box .table-action', function (e) {
    e.stopPropagation();
})

$(document).on('click','.btn-confirm', function (e) {
    $('#detailModal').modal('hide')
    $('#tableId').val(tableId);
    let total = $('#detail-total').html();
    let real_total = total.replaceAll(".", "")
    $('#consumption').val(real_total)
    $('#tip').val(parseInt(real_total * (tip_percentage/100)))
    $('#confirmModal').modal('show')
})

$(document).on('change','#consumption', function (e) {
    let total = $('#consumption').val();
    $('#tip').val(parseInt(total*(tip_percentage/100)))
})



$("#confirmForm").validate({
    validClass: "success",
    rules: {
        consumption:{
            required: true,
            number: true,
        },
        tip:{
            number: true
        },
        shipping:{
            number: true
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
            number: langs('messages.input_valid_number'),
        },
        tip:{
            number: langs('messages.input_valid_number'),
        },
        shipping:{
            number: langs('messages.input_valid_number'),
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
        title: langs('messages.are_you_sure'),
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

$(document).on('click', '.btn-print', function () {
    let selected = [];
    $('.orders').each(function () {
        let index = $(this).data('value');
        let checked = $('input[name=orders_'+index+']:checked').val();
        if (checked)
            selected.push(index)
    })
    var items = selected.toString();
    var appened = selected.length > 0 ? "?items=" + items : ''
    var url = HOST_URL + '/exportPdf/' + tableId + appened;

    $.ajax({
        url: url,
        type: 'get',
        contentType: false,
        processData: false,
        success: function(response){
            console.log(response);
            var url_pdf = HOST_URL+'/'+response.url_pdf;
            var url_png = HOST_URL+'/'+response.ticket_png;
            if(url_png.length>1){
                if(window.jspmWSStatus()){
                    //doPrintingPDF(url_pdf);
                    doPrinting(url_png);
                }
                swal({
                    buttons : {
                        confirm : {
                            className: 'btn btn-danger'
                        }
                    },
                    icon: url_png,
                });
                //window.open(url_png, '_blank');
                //window.open(url_pdf, '_blank');
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


$(document).on('click','.orders', function () {
    checkDisable()
})

$(document).on('click', '.btn-order-delete', function () {
    swal({
        title: langs('messages.sure_delete'),
        text: langs('messages.order_will_delete'),
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
            let selected = [];
            $('.orders').each(function () {
                let index = $(this).data('value');
                let checked = $('input[name=orders_'+index+']:checked').val();
                if (checked)
                    selected.push(index)
            })
            let formData = new FormData();
            formData.append('_token',_token);
            formData.append('tableId',tableId);
            formData.append('orders',selected.toString());
            $.ajax({
                url: path_delete_order,
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
                            getOrderList();
                        });
                    }else{
                        $('#detailModal').modal('hide');
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

$(document).on('click','.btn-deliver', function () {
    let selected = [];
    $('.orders').each(function () {
        let index = $(this).data('value');
        let checked = $('input[name=orders_'+index+']:checked').val();
        if (checked)
            selected.push(index)
    })
    showLoading()
    let formData = new FormData();
    formData.append('_token',_token);
    formData.append('tableId',tableId);
    formData.append('orders',selected.toString());
    $.ajax({
        url: path_mark_deliver,
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
                    getOrderList();
                });
            }else{
                $('#detailModal').modal('hide');
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

$(document).on('click', '.btn-confirm-close', function (e) {
    e.preventDefault();
    var formValues = $('#confirmForm').serialize();
    $.post(path_close_table, formValues, function(result){
        if(result.success){
            console.log(result);
            var url_png = HOST_URL+'/'+result.url_png;
            if(result.url_png.length > 1){
                if(window.jspmWSStatus()){
                    doPrinting(url_png);
                }
                $('#confirmModal').modal('hide')
                swal({
                    buttons : {
                        confirm : {
                            className: 'btn btn-danger'
                        }
                    },
                    icon: url_png,
                });
            }
            // setTimeout(function(){
            //     location.reload();
            // },7000);
        }else{
        }

    });
})
