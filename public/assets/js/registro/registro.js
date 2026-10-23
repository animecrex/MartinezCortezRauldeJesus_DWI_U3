const BASE_URL = $('meta[name="base-url"]').attr("content");

$(document).ready(function () {

    $("#registerForm").on("submit", function (event) {
        event.preventDefault();
        console.log("Interceptado");

        let formData = $(this).serialize();

        $.ajax({
            url: BASE_URL + "/register",
            type: "POST",
            data: formData,
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            success: function (response) {
                Swal.fire({
                    icon: "success",
                    title: "Éxito",
                    text: response.message
                });

                $("#registerForm")[0].reset();
            },
            error: function (xhr) {

                if (xhr.status === 422) {
                    let errores = xhr.responseJSON.errors;
                    let listaErrores = "";

                    Object.keys(errores).forEach(function (key) {
                        listaErrores += errores[key][0] + "<br>";
                    });

                    Swal.fire({
                        icon: "error",
                        title: "Errores",
                        html: listaErrores
                    });
                }
            }
        });

    });

});