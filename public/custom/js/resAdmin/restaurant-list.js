$(document).on('click', '.single-rest', function () {
    var id = $(this).data('index')

    location.href = HOST_URL + "/restaurant/detail/" + id
})

