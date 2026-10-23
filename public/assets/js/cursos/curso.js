const BASE_URL = ($('meta[name="base-url"]').attr("content") || "").replace(/\/$/, "");

$("#guardar_curso").on("click", function (event) {
    event.preventDefault();

    let formData = new FormData();

    formData.append("nombre", $("#nombre").val());
    formData.append("desc", $("#desc").val());
    formData.append("max_alumnos", $("#max_alumnos").val());
    formData.append("maestro", $("#maestro").val());
    formData.append("horas", $("#horas").val());
    formData.append("costo", $("#costo").val());
    formData.append("fecha_fin", $("#fecha_fin").val());
    formData.append("objetivos", $("#objetivos").val());
    formData.append("requisitos", $("#requisitos").val());
    formData.append("fecha_inicio", $("#fecha_inicio").val());

    let imagen = $("#imagen")[0]?.files[0];
    if (imagen !== undefined) {
        formData.append("imagen", imagen);
    }

    if (
        !$("#nombre").val() ||
        !$("#desc").val() ||
        !$("#max_alumnos").val() ||
        !$("#maestro").val() ||
        !$("#horas").val() ||
        !$("#costo").val() ||
        !$("#fecha_fin").val() ||
        !$("#objetivos").val() ||
        !$("#requisitos").val() ||
        !$("#fecha_inicio").val()
    ) {
        Swal.fire({
            icon: "warning",
            title: "Campos requeridos",
            text: "Por favor completa todos los campos para registrar el curso.",
            confirmButtonColor: "#2563eb",
        });
        return;
    }

    const $btn = $("#guardar_curso");
    const origText = $btn.html();
    $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span>Guardando...');

    $.ajax({
        url: BASE_URL + "/registrarcurso",
        type: "POST",
        data: formData,
        processData: false,
        contentType: false,
        cache: false,
        headers: {
            "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
        },
        success: function (response) {
            $btn.prop("disabled", false).html(origText);
            $("#registrarcurso")[0].reset();
            cargarTodos();
            Swal.fire({
                icon: "success",
                title: "¡Curso creado!",
                text: response.message || "El curso se ha registrado exitosamente.",
                timer: 2000,
                confirmButtonColor: "#2563eb",
            });
        },
        error: function (xhr) {
            $btn.prop("disabled", false).html(origText);
            console.error(xhr.responseText);
            Swal.fire({
                icon: "error",
                title: "Error al guardar",
                text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "No se pudo registrar el curso.",
                confirmButtonColor: "#2563eb",
            });
        },
    });
});

$(document).ready(function () {
    const DataTableFn = $.fn.DataTable || $.fn.dataTable;

    if (DataTableFn) {
        if (DataTableFn.isDataTable("#dt_search")) {
            $("#dt_search").DataTable().destroy();
        }

        window.t = $("#dt_search").DataTable({
            responsive: true,
            autoWidth: false,
            language: {
                processing: "Procesando...",
                search: "Buscar:",
                lengthMenu: "Mostrar _MENU_ registros",
                info: "Mostrando _START_ a _END_ de _TOTAL_ registros",
                infoEmpty: "Sin registros disponibles",
                infoFiltered: "(filtrado de _MAX_ registros)",
                emptyTable: "No has creado ningún curso todavía",
                zeroRecords: "No se encontraron coincidencias",
                paginate: {
                    first: "Primero",
                    previous: "Anterior",
                    next: "Siguiente",
                    last: "Último",
                },
            },
        });

        $("#buscador").on("keyup", function () {
            window.t.search(this.value).draw();
        });
    }

    cargarTodos();
});

function cargarTodos() {
    $.ajax({
        url: `${BASE_URL}/crearcurso/traercursos`,
        type: "GET",
        dataType: "json",
        success: function (res) {
            let dataSet = [];

            (res || []).forEach((r) => {
                const imgSrc = r.imagen 
                    ? `${BASE_URL}/storage/${r.imagen}` 
                    : `${BASE_URL}/assets/media/misc/curso.jpg`;

                dataSet.push([
                    `<div class="d-flex align-items-center gap-3">
                        <img src="${imgSrc}" class="rounded-3 object-fit-cover" style="width: 48px; height: 48px;" onerror="this.src='${BASE_URL}/assets/media/misc/curso.jpg'">
                        <div>
                            <strong class="text-dark d-block fs-6">${r.nombre}</strong>
                            <small class="text-muted"><i class="bi bi-clock me-1"></i>${r.horas || 1} hrs</small>
                        </div>
                    </div>`,
                    `<span class="badge-modern-primary"><i class="bi bi-person-fill me-1"></i>${r.maestro || 'No asignado'}</span>`,
                    `<div class="text-truncate text-muted" style="max-width: 200px;">${r.descripcion || '-'}</div>`,
                    `<span class="badge-modern-success fw-bold">$${parseFloat(r.costo || 0).toFixed(2)}</span>`,
                    `
                    <div class="d-flex align-items-center gap-2">
                        <a href="${BASE_URL}/detallescurso/${r.hash}" class="btn btn-sm btn-light-primary px-3 py-1 fw-bold">
                            <i class="bi bi-eye"></i> Ver
                        </a>
                        <button type="button" class="btn btn-sm btn-light-danger px-3 py-1 fw-bold btn-eliminar" data-id="${r.id}">
                            <i class="bi bi-trash"></i>
                        </button>
                    </div>
                    `,
                ]);
            });

            if (window.t) {
                window.t.clear().rows.add(dataSet).draw();
            }
        },
        error: function (xhr) {
            console.error("ERROR:", xhr.responseText);
        },
    });
}

$(document).on("click", ".btn-eliminar", function () {
    let id = $(this).data("id");
    eliminarCurso(id);
});

function eliminarCurso(id) {
    Swal.fire({
        title: "¿Eliminar este curso?",
        text: "Esta acción no se puede deshacer y se borrarán las suscripciones asociadas.",
        icon: "warning",
        showCancelButton: true,
        confirmButtonColor: "#ef4444",
        cancelButtonColor: "#64748b",
        confirmButtonText: "Sí, eliminar curso",
        cancelButtonText: "Cancelar",
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: `${BASE_URL}/crearcurso/eliminarcurso/${id}`,
                type: "POST",
                headers: {
                    "X-CSRF-TOKEN": $('meta[name="csrf-token"]').attr("content"),
                },
                success: function (res) {
                    cargarTodos();
                    Swal.fire({
                        title: "¡Eliminado!",
                        text: res.message || "El curso fue eliminado correctamente.",
                        icon: "success",
                        confirmButtonColor: "#2563eb",
                        timer: 1500,
                    });
                },
                error: function (xhr) {
                    Swal.fire({
                        title: "Error",
                        text: "No se pudo eliminar el curso.",
                        icon: "error",
                        confirmButtonColor: "#2563eb",
                    });
                }
            });
        }
    });
}
