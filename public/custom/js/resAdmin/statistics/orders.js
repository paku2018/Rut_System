$('.datepicker').datetimepicker({
    format: 'MM/DD/YYYY',
});

$(document).ready(function () {
    $('[name="end_date"]').val(end_date);
    $('[name="start_date"]').val(start_date);
})


$('#search').on('click', function () {
    let from_date = $('#start_date').val();
    let arr = from_date.split("/")
    let start = arr[2] + "-" + arr[0] + "-" + arr[1]
    let to_date = $('#end_date').val();
    let spl = to_date.split("/")
    let end = spl[2] + "-" + spl[0] + "-" + spl[1]

    location.href = link + "?start=" + start + "&&end=" + end;
})
