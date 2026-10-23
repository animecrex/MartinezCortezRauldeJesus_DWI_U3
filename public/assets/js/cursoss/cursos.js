const BASE_URL = ($('meta[name="base-url"]').attr("content") || "").replace(/\/$/, "");
const csrfToken = $('meta[name="csrf-token"]').attr("content");

let cursosCache = [];

$(document).ready(function () {
    cargarTodos();
    verificarEstadoSuscripcion();

    // Live search listener
    $(document).on("input", "#searchCursosInput", function () {
        const query = $(this).val().toLowerCase().trim();
        renderCursos(query);
    });
});

function renderCursos(filter = "") {
    let filtrados = cursosCache;
    if (filter) {
        filtrados = cursosCache.filter((c) => {
            const nombre = (c.nombre || "").toLowerCase();
            const desc = (c.descripcion || "").toLowerCase();
            const maestro = (c.maestro || "").toLowerCase();
            return nombre.includes(filter) || desc.includes(filter) || maestro.includes(filter);
        });
    }

    if (!filtrados.length) {
        $("#contenedor-cursos").html(`
            <div class="col-12 text-center py-12">
                <div class="d-inline-flex align-items-center justify-content-center p-4 bg-light rounded-circle mb-3">
                    <i class="bi bi-search fs-2x text-muted"></i>
                </div>
                <h4 class="fw-bold text-dark mb-1">No se encontraron cursos</h4>
                <p class="text-muted fs-6">Intenta con otros términos o registra un nuevo curso.</p>
            </div>
        `);
        return;
    }

    let html = "";
    filtrados.forEach((r) => {
        const imgSrc = r.imagen 
            ? `${BASE_URL}/storage/${r.imagen}` 
            : `${BASE_URL}/assets/media/misc/curso.jpg`;

        const costoFormat = parseFloat(r.costo || 0).toLocaleString('es-MX', {
            minimumFractionDigits: 2,
            maximumFractionDigits: 2
        });

        html += `
            <div class="course-card">
                <div class="course-card-img-wrapper">
                    <img src="${imgSrc}" class="course-card-img" alt="${r.nombre}" onerror="this.src='${BASE_URL}/assets/media/misc/curso.jpg'">
                    <span class="course-card-badge-cost">
                        $${costoFormat} MXN
                    </span>
                </div>

                <div class="course-card-body">
                    <h3 class="course-card-title">${r.nombre}</h3>
                    <p class="course-card-desc">${r.descripcion || 'Sin descripción disponible.'}</p>

                    <div class="mb-3">
                        <div class="course-meta-item">
                            <i class="bi bi-person-workspace"></i>
                            <span>${r.maestro ? r.maestro : 'Instructor asignado'}</span>
                        </div>
                        <div class="course-meta-item">
                            <i class="bi bi-clock-history text-warning"></i>
                            <span>${r.horas || 1} horas lectivas</span>
                        </div>
                        <div class="course-meta-item">
                            <i class="bi bi-people text-info"></i>
                            <span>Cupo: ${r.cant_alumnos || 'Limitado'} alumnos</span>
                        </div>
                    </div>

                    <div class="course-card-footer">
                        <a class="btn-modern-primary w-100 text-center text-decoration-none" 
                           href="${BASE_URL}/detallescurso/${r.hash}">
                            <span>Ver Curso & Inscribirse</span>
                            <i class="bi bi-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        `;
    });

    $("#contenedor-cursos").html(html);
}

function cargarTodos() {
    $("#contenedor-cursos").html(`
        <div class="col-12 text-center py-10">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="text-muted mt-2 fs-7">Cargando cursos disponibles...</p>
        </div>
    `);

    $.ajax({
        url: `${BASE_URL}/curso/traercursos`,
        type: "GET",
        dataType: "json",
        success: function (res) {
            cursosCache = res || [];
            $("#totalCursosCount").text(cursosCache.length);
            renderCursos();
        },
        error: function (xhr) {
            console.error("ERROR:", xhr.responseText);
            $("#contenedor-cursos").html(`
                <div class="col-12 alert alert-danger text-center">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    No se pudieron cargar los cursos. Verifica la conexión a la base de datos.
                </div>
            `);
        },
    });
}

$(document).on("click", ".pagarBtn", function () {
    let cursoId = $(this).data("curso-id");
    let $btn = $("#modalPago").data("btn-target") || $("#btn-inscripcion");
    const tarjetaId = $("#tarjetaSelect").val();

    if (!tarjetaId) {
        Swal.fire({
            title: "Selecciona una tarjeta",
            text: "Debes elegir una tarjeta bancaria para autorizar la inscripción.",
            icon: "warning",
            confirmButtonColor: "#2563eb",
            confirmButtonText: "Entendido",
        });
        return;
    }

    registrarCurso(cursoId, $btn, tarjetaId);
    cerrarModalPago();
});

$(document).on("click", "#btn-inscripcion", function () {
    const cursoId = $(this).data("curso-id");
    const suscrito = $(this).data("suscrito") === true;

    if (suscrito) {
        Swal.fire({
            title: "¿Deseas darte de baja?",
            text: "Se cancelará tu inscripción a este curso.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#ef4444",
            cancelButtonColor: "#64748b",
            confirmButtonText: "Sí, desinscribirme",
            cancelButtonText: "Cancelar",
        }).then((result) => {
            if (result.isConfirmed) {
                desregistrarCurso(cursoId, $(this));
            }
        });
    } else {
        abrirModalPago(cursoId, $(this));
    }
});

function verificarEstadoSuscripcion() {
    const cursoId = $("#btn-inscripcion").data("curso-id");

    if (!cursoId) {
        return;
    }

    $.ajax({
        url: `${BASE_URL}/suscripcion/estado`,
        type: "GET",
        dataType: "json",
        data: { id_curso: cursoId },
        success: function (res) {
            actualizarBotonInscripcion(res.suscrito, $("#btn-inscripcion"));
        },
        error: function (xhr) {
            console.error("ERROR:", xhr.responseText);
        },
    });
}

function actualizarBotonInscripcion(suscrito, $btn) {
    if (!$btn || !$btn.length) {
        return;
    }

    $btn.data("suscrito", suscrito);
    if (suscrito) {
        $btn.html('<i class="bi bi-x-circle me-1"></i> Desuscribirme');
        $btn.removeClass("btn-modern-primary btn-primary").addClass("btn-danger");
    } else {
        $btn.html('<i class="bi bi-check2-circle me-1"></i> Inscribirme al Curso');
        $btn.removeClass("btn-danger").addClass("btn-modern-primary");
    }
}

function abrirModalPago(cursoId, $btn) {
    $("#modalPago").data("curso-id", cursoId);
    $("#modalPago").data("btn-target", $btn || null);
    $(".pagarBtn").data("curso-id", cursoId);
    $("#modalPagoBackdrop").fadeIn(200);
}

function cerrarModalPago() {
    $("#modalPagoBackdrop").fadeOut(200);
}

function registrarCurso(cursoId, $btn, tarjetaId) {
    $.ajax({
        url: `${BASE_URL}/suscribirse`,
        type: "POST",
        dataType: "json",
        data: {
            id_curso: cursoId,
            id_tarjeta: tarjetaId,
            _token: csrfToken,
        },
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        success: function (res) {
            actualizarBotonInscripcion(true, $btn);
            Swal.fire({
                title: "¡Inscripción Exitosa!",
                text: res.message || "Tu curso ha sido registrado exitosamente.",
                icon: "success",
                confirmButtonColor: "#2563eb",
                confirmButtonText: "Ir a Mis Suscripciones",
            }).then(() => {
                window.location.href = `${BASE_URL}/mis-suscripciones`;
            });
        },
        error: function (xhr) {
            console.error("ERROR:", xhr.responseText);
            Swal.fire({
                title: "Error al inscribir",
                text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "No se pudo procesar la inscripción.",
                icon: "error",
                confirmButtonColor: "#2563eb"
            });
        },
    });
}

function desregistrarCurso(cursoId, $btn) {
    $.ajax({
        url: `${BASE_URL}/desuscribirse`,
        type: "POST",
        dataType: "json",
        data: {
            id_curso: cursoId,
            _token: csrfToken,
        },
        headers: {
            "X-CSRF-TOKEN": csrfToken,
        },
        success: function (res) {
            actualizarBotonInscripcion(false, $btn);
            Swal.fire({
                title: "Desinscripción completada",
                text: res.message || "Has sido dado de baja de este curso.",
                icon: "info",
                confirmButtonColor: "#2563eb",
            });
        },
        error: function (xhr) {
            console.error("ERROR:", xhr.responseText);
        },
    });
}

// Modal closing handlers
$(document).on("click", "#cerrarModalBtn, .btn-close-modal", function () {
    cerrarModalPago();
});

$(document).on("click", "#modalPagoBackdrop", function (e) {
    if ($(e.target).is("#modalPagoBackdrop")) {
        cerrarModalPago();
    }
});