var tableId = 0;
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
                    let code = '';
                    let total = 0;
                    if (orders.length > 0){
                        if(data.status == "ordered")
                            code += '<div class="w-100 text-right mb-2"><button class="btn btn-black btn-round btn-deliver btn-sm"><i class="fa fa-check mr-2"></i>' + langs('messages.mark_as_deliver') + '</button></div>'
                        for (let i=0; i<orders.length; i++){
                            let val = orders[i].product.sale_price * orders[i].order_count;
                            code += ' <div class="d-flex justify-content-between">\n' +
                                '                                    <h4 class="text-danger mb-1">' + orders[i].product.name + '</h4>\n' +
                                '                                    <h4 class="mb-1">' + orders[i].product.sale_price + '*' + orders[i].order_count + '='+ val +'</h4>\n' +
                                '                                </div>'

                            total += val;
                        }
                    }
                    $('#assigned-orders').html(code);
                    $('#detail-total').html(total);
                    checkCount()
                    $('.btn-pend').prop('disabled', false)
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
    showLoading()
    let formData = new FormData();
    formData.append('_token',_token);
    formData.append('tableId',tableId);
    $.ajax({
        url: path_mark_deliver,
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
    $('#new-total').html(new_total)
}

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

$(document).on('click','.btn-pend', function () {
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
})

$(document).ready(function () {
    $('#dt_table').DataTable({
        "pageLength": 10,
        "lengthChange": false,
        "order": [[ 1, 'asc' ]],
        "aoColumnDefs": [
            { 'bSortable': false, 'aTargets': [ 0, 3 ] }
        ]
    });
})

$(document).on('click', '.btn-print', function () {
    var url = HOST_URL + '/exportPdf/' + tableId;

    window.open(url, '_blank');
})
