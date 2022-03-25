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

$('#email').on('change', function(e) {
    checkCount()
});

function getOrderData() {
    if (tableId == ""){
        return
    }
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

function generateOrderedList(tableData, orders){
    let code = '';
    let total = 0;
    let comment = '';
    let comment_list = [];
    if (orders.length > 0){
        code += '<div class="w-100 mb-2">'
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
            code += ' <div class="w-100 pb-1" style="border-bottom: 1px solid #eeeeee"><div class="d-flex align-items-center w-100">\n' +
                '                                    <h4 class="mb-0 ' + direct_order_class + '">' + orders[i].product.name + delivered + '</h4></div>\n' +
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
    $('#assigned-orders').append(comment)
    $('#detail-total').html(total.toLocaleString('de-DE'));
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
    let email = $('#email').val();
    let name = $('#name').val();
    let address = $('#address').val();
    let formData = new FormData();
    formData.append('items',JSON.stringify(items));
    formData.append('email',email);
    formData.append('name',name);
    formData.append('address',address);
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
                    $('.btn-order').attr('disabled', true)
                    $('.btn-print').attr('disabled', false)
                    tableId = response.data.id;

                    getOrderData()
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
