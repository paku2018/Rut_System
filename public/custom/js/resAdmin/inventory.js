$(document).ready(function () {

})

var addedItems = [];
var addedIdx = 0;

function refreshTable() {
    let filter = $('#product_code').val()

    try {
        dt.destroy()
    } catch (e) {
    }
    dt = $('#dt_table').DataTable({
        ajax: {
            url: path_get_products,
            type: "POST",
            data: {filter: filter, _token: _token},
            dataSrc: ""
        },
        "pageLength": 10,
        "order": [[ 0, 'desc' ]],
        columns: [
            {"data": "name"},
            {"data": "sale_price"},
            {"data": "stock_count"},
            {"data": "id","render":function (data, type, row) {
                var code = '<button class="btn btn-black btn-add" data-index="' + row.id + '" data-name="' + row.name + '" data-price="' + row.sale_price + '" data-stock="' + row.stock_count + '">' + langs('messages.add') + '</button>'
                    return code
            }},
        ]
    });
}

function getProducts() {
    refreshTable()

    $('#productsModal').modal('show')
}

$(document).on('click', '.btn-add', function () {
    var id = $(this).data('index')
    var name = $(this).data('name')
    var price = $(this).data('price')
    var stock = $(this).data('stock')
    var item = {
        idx: addedIdx, product_id: id, name: name, price: price, quantity: 1, total: price
    }

    var code = '<tr>\n' +
        '                                                        <td>\n' +
        '                                                            <div class="form-group mb-0">\n' +
        '                                                                <input type="text" class="form-control descriptions" value="' + name + '" disabled>\n' +
        '                                                            </div>\n' +
        '                                                        </td>\n' +
        '                                                        <td>' + price + '</td>\n' +
        '                                                        <td>' + stock + '</td>\n' +
        '                                                        <td>\n' +
        '                                                            <div class="quantity d-flex">\n' +
        '                                                                <button class="minus-btn" type="button" name="button" data-index="' + addedIdx + '">-</button>\n' +
        '                                                                <input type="text" class="sale_quantity" min="0" value="1" data-index="' + addedIdx + '" id="quantity_' + addedIdx + '">\n' +
        '                                                                <button class="plus-btn" type="button" name="button" data-index="' + addedIdx + '">+</button>\n' +
        '                                                            </div>\n' +
        '                                                        </td>\n' +
        '                                                        <td id="total_' + addedIdx + '">' + price + '</td>\n' +
        '                                                    </tr>'

    addedIdx++;
    addedItems.push(item)
    $('#tb_added_products').append(code)
    $('#added_products').removeClass('d-none')

    $('#productsModal').modal('hide')
})

$(document).on('click', '.minus-btn', function () {
    var idx = $(this).data('index')
    var quantity = addedItems[idx].quantity
    if (quantity > 1) {
        quantity = quantity - 1
    }else {
        quantity = 0
    }
    addedItems[idx].quantity = quantity
    addedItems[idx].total = quantity * addedItems[idx].price

    $('#quantity_' + idx).val(quantity)
    $('#total_' + idx).html(addedItems[idx].total)
})

$(document).on('click', '.plus-btn', function () {
    var idx = $(this).data('index')
    var quantity = addedItems[idx].quantity
    quantity++

    addedItems[idx].quantity = quantity
    addedItems[idx].total = quantity * addedItems[idx].price

    $('#quantity_' + idx).val(quantity)
    $('#total_' + idx).html(addedItems[idx].total)
})

$(document).on('change', '.sale_quantity', function () {
    var idx = $(this).data('index')
    var val = $(this).val()

    addedItems[idx].quantity = val
    addedItems[idx].total = addedItems[idx].quantity * addedItems[idx].price

    $('#total_' + idx).html(addedItems[idx].total)
})

function addNormal() {
    let amount = $('#amount').val()
    if (amount == 0) {
        alert(langs('messages.input_amount'))
        return
    }

    var item = {
        idx: addedIdx, product_id: 0, name: '', price: amount, quantity: 1, total: amount
    }

    var code = '<tr>\n' +
        '                                                        <td>\n' +
        '                                                            <div class="form-group mb-0">\n' +
        '                                                                <input type="text" class="form-control" id="product_name_' + addedIdx + '">\n' +
        '                                                            </div>\n' +
        '                                                        </td>\n' +
        '                                                        <td>' + amount + '</td>\n' +
        '                                                        <td></td>\n' +
        '                                                        <td>\n' +
        '                                                            <div class="quantity d-flex">\n' +
        '                                                                <button class="minus-btn" type="button" name="button" data-index="' + addedIdx + '">-</button>\n' +
        '                                                                <input type="text" class="sale_quantity" min="0" value="1" data-index="' + addedIdx + '" id="quantity_' + addedIdx + '">\n' +
        '                                                                <button class="plus-btn" type="button" name="button" data-index="' + addedIdx + '">+</button>\n' +
        '                                                            </div>\n' +
        '                                                        </td>\n' +
        '                                                        <td id="total_' + addedIdx + '">' + amount + '</td>\n' +
        '                                                    </tr>'

    addedIdx++;
    addedItems.push(item)
    $('#tb_added_products').append(code)
    $('#added_products').removeClass('d-none')
}
