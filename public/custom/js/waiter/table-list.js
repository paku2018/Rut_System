var tableId = 0;

$(document).ready(function () {
    getTableList()
    setInterval(function () {
        getTableList()
    }, 10000);
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
                if(data.status == "open" || data.status == "closed" || data.status == "ordered"){
                    let orders = response.orders;

                    generateOrderedList(data, orders)

                    checkCount()
                    checkDisable()
                    $('.btn-pend').prop('disabled', false)
                    if (orders.length == 0) {
                        $('.btn-pend').attr('disabled', true)
                    }else {
                        $('.btn-pend').attr('disabled', false)
                    }
                    $('#detailModal').modal('show')
                }else{
                    swal(langs('messages.wait_cashier_close_table'), {
                        icon: "info",
                        buttons : {
                            confirm : {
                                className: 'btn btn-black'
                            }
                        }
                    })
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
            //let direct_order_class = orders[i].direct == null? 'text-danger':'text-purple'
            let sub_orders = orders[i].children
            let sub_code = ''
            if (sub_orders.length > 0) {
                for (let ii=0; ii<sub_orders.length; ii++) {
                    sub_code += '<div class="d-flex justify-content-between">' +
                        '<h4 class="mb-0 ml-3 text-danger">' + sub_orders[ii].detail.name +'</h4>' +
                        '<h4 class="mb-0 text-danger">' + sub_orders[ii].detail.sale_price.toLocaleString('de-DE') +'</h4>' +
                        '</div>'
                    total += sub_orders[ii].detail.sale_price
                }
            }
            code += ' <div class="pb-1" style="border-bottom: 1px solid #eeeeee"><div class="d-flex align-items-center mb-1">\n' +
                '                                    <input type="checkbox" name="orders_'+orders[i].id+'" class="orders mr-2" data-value="'+ orders[i].id +'">' +
                '                                    <h4 class="mb-0">' + orders[i].product.name + delivered + '</h4></div>\n' +
                '                                    <h4 class="text-right mb-1">' + orders[i].product.sale_price.toLocaleString('de-DE') + '*' + orders[i].order_count + '='+ val.toLocaleString('de-DE') +'</h4>'
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
    $('#assigned-orders').append(comment);
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

                if (orders.length == 0) {
                    $('.btn-pend').attr('disabled',true)
                }else {
                    $('.btn-pend').attr('disabled',false)
                }
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
                        code += '</div>'
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

$(document).on('click','.btn-next', function () {
    $('#optionModal').modal('hide');
    var checked = $('input[name=orderType]:checked').val();
    if(checked == 1){
        let formData = new FormData();
        formData.append('_token',_token);
        $.ajax({
            url: path_get_orders,
            type: 'post',
            data: formData,
            contentType: false,
            processData: false,
            success: function(response){
                if(response.result){
                    let code = '';
                    let data = response.data;
                    if (data.length > 0){
                        code = '<div class="table-responsive"><table class="table table-striped"><tbody>'
                        code += '<tr>\n' +
                            '<td><input type="checkbox" name="select_all" class="all"></td>\n' +
                            '<td>' + langs('messages.client') + '</td>\n' +
                            '<td>' + langs('messages.product') + '</td>\n' +
                            '<td>' + langs('messages.image') + '</td>\n' +
                            '<td>' + langs('messages.price') + '</td>\n' +
                            '<td>' + langs('messages.count') + '</td>\n' +
                            '</tr>\n';
                        for (let i=0; i<data.length; i++){
                            code += '<tr>'
                            code += '<td><input type="checkbox" name="items_'+data[i].id+'" class="items" data-value="'+ data[i].id +'" data-price="'+ data[i].product.sale_price +'" data-count="'+ data[i].order_count +'" value="'+ data[i].id +'"></td>'
                            code += '<td>' + data[i].client.name + '</td>'
                            code += '<td>' + data[i].product.name + '</td>'
                            code += '<td> <img src="' + data[i].product.image + '" alt="no_img" class="preview-image"></td>'
                            code += '<td>' + data[i].product.sale_price + '</td>'
                            code += '<td>' + data[i].order_count + '</td>'
                            code += '</tr>'
                        }
                        code += '</tbody></table></div>';
                        code += '<h4 class="text-right">'+langs('messages.total')+' : <span id="total-price"></span></h4>'
                    }else{
                        code = '<h3 class="text-center">Sin ordenes</h3>'
                    }
                    $('#order-list').html(code);
                    $('#existingModal').modal('show')
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
                disableAssign()
            },
        });
    }
    else{
        checkCount();
        $('#assigned-orders').html('');
        $('#detail-total').html(0)
        $('.btn-pend').prop('disabled', true)
        setTimeout(function(){$('#detailModal').modal('show');}, 500);
    }
})
$(document).on('click', '.all', function () {
    var checked = $('input[name=select_all]:checked').val();
    checked = checked=="on"?true:false;
    $('.items').each(function () {
        $(this).prop('checked', checked);
    })
    disableAssign()
})

function disableAssign(){
    let checked_count = 0;
    let total_count = 0;
    let total = 0;
    $('.items').each(function () {
        total_count ++;
        let index = $(this).data('value');
        let checked = $('input[name=items_'+index+']:checked').val();
        let price;
        let count;
        if (checked){
            checked_count++;
            price = $(this).data('price')
            count = $(this).data('count')
            total += price*count;
        }
    })
    if (checked_count == 0){
        $('.btn-assign').prop('disabled', true)
    }else{
        $('.btn-assign').prop('disabled', false)
    }

    //calculate total
    if(total_count > 0){
        $('#total-price').html(total);
    }
}
$(document).on('click','.items', function () {
    disableAssign()
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

$(document).on('click','.btn-assign', function () {
    showLoading()
    let selected = [];
    $('.items').each(function () {
        let index = $(this).data('value');
        let checked = $('input[name=items_'+index+']:checked').val();
        if (checked)
            selected.push(index)
    })
    let formData = new FormData();
    formData.append('_token',_token);
    formData.append('tableId',tableId);
    formData.append('orders',selected.toString());
    $.ajax({
        url: path_assign_orders,
        type: 'post',
        data: formData,
        contentType: false,
        processData: false,
        success: function(response){
            hideLoading()
            if(response.result){
                $('#existingModal').modal('hide');
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

    $('#new-total').html(new_total)
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

$(document).on('click','.btn-pend', function () {
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
                url: path_pend_table,
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

$(document).ready(function () {
    $('#dt_table').DataTable({
        "pageLength": 3,
        "lengthChange": false,
        "order": [[ 1, 'asc' ]],
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [ 0, 3 ] }
        ],
        language: {
            url: path_lang_datatable
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

    //window.open(url, '_blank');
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
