$('.datepicker').datetimepicker({
    format: 'MM/DD/YYYY',
});

$(document).ready(function () {
    $('[name="end_date"]').val(window.moment().format('MM/DD/YYYY'));
    $('[name="start_date"]').val(window.moment().subtract(1, 'months').add(1, 'days').format('MM/DD/YYYY'));

    refreshTable()
})

function refreshTable() {
    let from_date = $('#start_date').val();
    let arr = from_date.split("/")
    let start_date = arr[2] + "-" + arr[0] + "-" + arr[1]
    let to_date = $('#end_date').val();
    let spl = to_date.split("/")
    let end = spl[2] + "-" + spl[0] + "-" + spl[1]
    let end_date = moment(end).add(1,'days').format("YYYY-MM-DD");

    try {
        dt.destroy()
    } catch (e) {
    }
    dt = $('#dt_table').DataTable({
        ajax: {
            url: path_data,
            type: "POST",
            data: {start_date: start_date, end_date: end_date, _token: _token},
            dataSrc: ""
        },
        "pageLength": 10,
        "order": [[ 5, 'desc' ]],
        columns: [
            {"data": "id"},
            {"data": "table","render":function (data) {
                if (data)
                    return data.name;
                else
                    return ''
            }},
            {"data": "consumption"},
            {"data": "tip"},
            {"data": "shipping"},
            {"data": "created_at","render":function (data) {
                if (data)
                    return window.moment(data).format('YYYY-MM-DD');
                else
                    return ''
            }},
        ]
    });
}

$('#search').on('click', function () {
    refreshTable()
})
