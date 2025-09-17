function sweetAlertAjax(type, url, title) {
    swal({
        title: title,
        icon: "warning",
        buttons: true,
        dangerMode: true,
    }).then((willDelete) => {
        if (willDelete) {
            $.ajax({
                url: url,
                type: type,
                data: {
                    "_token": $("#csrf-token").val(),
                },
                success: function (response) {
                    if (response.status == 400) {
                        swal({
                            text: response.message,
                            icon: "error",
                            button: "ok",
                        }).then(function () {
                            location.reload();
                        });
                    }
                    else {
                        swal({
                            text: response.message,
                            icon: "success",
                            button: "Ok",
                        }).then(function () {
                            location.reload();
                        });
                    }
                }
            });
        }
    });
}