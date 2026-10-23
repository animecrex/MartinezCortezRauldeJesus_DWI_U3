<!-- Navigation Back Button -->
<div class="mb-4 d-flex align-items-center justify-content-between">
    <a href="{{ route('curso') }}" class="btn-modern-outline text-decoration-none">
        <i class="bi bi-arrow-left"></i>
        <span>Volver al Catálogo</span>
    </a>

    <span class="badge-modern-primary">
        <i class="bi bi-shield-check me-1"></i> Curso Oficial
    </span>
</div>

<div class="row g-6">
    <!-- Left Column: Course Main Details -->
    <div class="col-lg-8">
        <div class="modern-card mb-6">
            <!-- Course Hero Image -->
            <div style="height: 320px; overflow: hidden; position: relative; background: #0f172a;">
                @if($curso->imagen)
                    <img src="{{ asset('storage/' . $curso->imagen) }}" class="w-100 h-100 object-fit-cover" alt="{{ $curso->nombre }}">
                @else
                    <img src="{{ asset('assets/media/misc/curso.jpg') }}" class="w-100 h-100 object-fit-cover opacity-80" alt="{{ $curso->nombre }}">
                @endif
                <div style="position: absolute; inset: 0; background: linear-gradient(180deg, transparent 40%, rgba(15, 23, 42, 0.9) 100%);"></div>
                <div style="position: absolute; bottom: 1.5rem; left: 2rem; right: 2rem;">
                    <span class="badge bg-primary px-3 py-2 rounded-pill mb-2">Formación Académica</span>
                    <h1 class="text-white fw-bolder fs-2hx mb-0">{{ $curso->nombre }}</h1>
                </div>
            </div>

            <div class="p-5 p-md-8">
                <!-- Instructor & Meta row -->
                <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 p-4 bg-light rounded-4 mb-6">
                    <div class="d-flex align-items-center gap-3">
                        <div class="user-avatar-circle" style="width: 48px; height: 48px; font-size: 1.25rem;">
                            {{ strtoupper(substr($curso->maestro ?? 'M', 0, 1)) }}
                        </div>
                        <div>
                            <span class="text-muted fs-8 text-uppercase fw-bold">Instructor Titular</span>
                            <div class="fw-bold text-dark fs-6">{{ $curso->maestro ?? 'Profesor Asignado' }}</div>
                        </div>
                    </div>

                    <div class="d-flex gap-4">
                        <div>
                            <span class="text-muted fs-8 text-uppercase fw-bold d-block">Duración</span>
                            <span class="fw-bold text-dark"><i class="bi bi-clock-history text-warning me-1"></i>{{ $curso->horas }} horas</span>
                        </div>
                        <div>
                            <span class="text-muted fs-8 text-uppercase fw-bold d-block">Cupo Máximo</span>
                            <span class="fw-bold text-dark"><i class="bi bi-people-fill text-info me-1"></i>{{ $curso->cant_alumnos }} alumnos</span>
                        </div>
                    </div>
                </div>

                <!-- Description -->
                <div class="mb-6">
                    <h3 class="fw-bold text-dark fs-4 mb-3">Descripción General</h3>
                    <p class="text-muted fs-6 lh-lg mb-0">
                        {{ $curso->descripcion ?: 'En este curso aprenderás los conceptos y técnicas fundamentales guiado paso a paso por instructores calificados.' }}
                    </p>
                </div>

                <div class="row g-4 mb-6">
                    <!-- Objetivos -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 border border-light-subtle h-100 bg-white">
                            <h4 class="fw-bold text-dark fs-5 mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-bullseye text-primary"></i>
                                <span>Objetivos del Curso</span>
                            </h4>
                            <p class="text-muted fs-7 lh-base mb-0">
                                {{ $curso->objetivos ?: 'Dominar los conceptos clave, desarrollar proyectos prácticos y adquirir herramientas aplicables en el ámbito profesional.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Requisitos -->
                    <div class="col-md-6">
                        <div class="p-4 rounded-4 border border-light-subtle h-100 bg-white">
                            <h4 class="fw-bold text-dark fs-5 mb-3 d-flex align-items-center gap-2">
                                <i class="bi bi-check2-circle text-success"></i>
                                <span>Requisitos Previos</span>
                            </h4>
                            <p class="text-muted fs-7 lh-base mb-0">
                                {{ $curso->requisitos ?: 'Disposición para aprender, equipo con conexión a internet y conocimientos básicos relacionados con la temática.' }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Fechas importantes -->
                <div class="p-4 rounded-4 bg-light-subtle border border-dashed border-gray-300">
                    <div class="row g-3 text-center">
                        <div class="col-6">
                            <span class="text-muted fs-8 text-uppercase fw-bold d-block">Fecha de Inicio</span>
                            <span class="fw-bold text-dark fs-6">
                                <i class="bi bi-calendar-event text-primary me-1"></i>
                                {{ $curso->fecha_inicio ? date('d/m/Y', strtotime($curso->fecha_inicio)) : 'A definir' }}
                            </span>
                        </div>
                        <div class="col-6">
                            <span class="text-muted fs-8 text-uppercase fw-bold d-block">Fecha de Término</span>
                            <span class="fw-bold text-dark fs-6">
                                <i class="bi bi-calendar-check text-success me-1"></i>
                                {{ $curso->fecha_fin ? date('d/m/Y', strtotime($curso->fecha_fin)) : 'A definir' }}
                            </span>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Right Column: Sticky Enrollment Box -->
    <div class="col-lg-4">
        <div class="modern-card sticky-top" style="top: 90px;">
            <div class="p-5 p-md-6">
                <span class="text-muted fs-7 fw-semibold d-block mb-1">Inversión del Programa</span>
                <div class="d-flex align-items-baseline gap-2 mb-4">
                    <span class="display-6 fw-bolder text-dark">
                        ${{ number_format($curso->costo, 2) }}
                    </span>
                    <span class="text-muted fs-7 fw-semibold">MXN</span>
                </div>

                <div class="d-grid mb-4">
                    <button type="button" class="btn-modern-primary py-3 fs-6 w-100 justify-content-center" 
                            id="btn-inscripcion" data-curso-id="{{ $curso->id }}">
                        <span class="spinner-border spinner-border-sm me-2" role="status"></span>
                        Verificando estado...
                    </button>
                </div>

                <div class="d-flex flex-column gap-3 py-3 border-top border-bottom border-light-subtle mb-4 fs-7 text-muted">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-patch-check-fill text-primary"></i>
                        <span>Acceso completo a materiales y clases</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-award-fill text-warning"></i>
                        <span>Constancia de acreditación oficial</span>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-shield-lock-fill text-success"></i>
                        <span>Garantía de satisfacción y pago seguro</span>
                    </div>
                </div>

                <div class="text-center">
                    <span class="text-muted fs-8">
                        <i class="bi bi-lock-fill me-1"></i> Transacción cifrada con encriptación SSL
                    </span>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modern Subscription & Payment Modal -->
<div class="custom-modal-backdrop" id="modalPagoBackdrop">
    <div class="custom-modal-box" id="modalPago">
        <div class="p-4 p-md-6 border-bottom d-flex align-items-center justify-content-between">
            <div class="d-flex align-items-center gap-2">
                <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem;">
                    <i class="bi bi-credit-card-2-front"></i>
                </div>
                <h4 class="fw-bold text-dark mb-0 fs-5">Confirmar Inscripción</h4>
            </div>
            <button type="button" class="btn btn-icon btn-light btn-sm rounded-circle btn-close-modal" aria-label="Cerrar">
                <i class="bi bi-x-lg"></i>
            </button>
        </div>

        <div class="p-4 p-md-6">
            <div class="p-3 bg-light rounded-4 mb-4">
                <div class="fs-7 text-muted mb-1">Curso seleccionado:</div>
                <div class="fw-bold text-dark fs-6">{{ $curso->nombre }}</div>
                <div class="d-flex justify-content-between align-items-center mt-2 pt-2 border-top">
                    <span class="text-muted fs-7">Monto a pagar:</span>
                    <span class="fw-bolder text-primary fs-5">${{ number_format($curso->costo, 2) }} MXN</span>
                </div>
            </div>

            <label class="form-label-modern" for="tarjetaSelect">Selecciona tu método de pago:</label>
            <div class="mb-4">
                <select class="form-select form-select-modern" id="tarjetaSelect">
                    <option value="">-- Seleccionar tarjeta registrada --</option>
                    @foreach ($tarjetas as $tarjeta)
                        <option value="{{ $tarjeta->id }}">
                            💳 **** **** **** {{ substr($tarjeta->numero_tarjeta, -4) }} ({{ $tarjeta->banco }})
                        </option>
                    @endforeach
                </select>
            </div>

            @if ($tarjetas->isEmpty())
                <div class="alert alert-warning d-flex align-items-center gap-3 p-3 rounded-3 mb-4">
                    <i class="bi bi-exclamation-triangle-fill fs-4 flex-shrink-0"></i>
                    <div class="fs-7">
                        No tienes ninguna tarjeta registrada.
                        <a href="{{ route('vistaperfil') }}" class="fw-bold text-dark text-decoration-underline ms-1">Registrar tarjeta aquí</a>
                    </div>
                </div>
            @endif

            <div class="d-flex align-items-center gap-3 mt-4">
                <button type="button" class="btn-modern-outline flex-grow-1 justify-content-center btn-close-modal">
                    Cancelar
                </button>
                <button type="button" class="btn-modern-primary flex-grow-1 justify-content-center pagarBtn" 
                        data-curso-id="{{ $curso->id }}" {{ $tarjetas->isEmpty() ? 'disabled' : '' }}>
                    <i class="bi bi-check2-circle me-1"></i> Autorizar Pago
                </button>
            </div>
        </div>
    </div>
</div>
