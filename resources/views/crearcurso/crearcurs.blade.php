<div class="p-4 p-md-6">
    <div class="d-flex align-items-center gap-2 mb-4 pb-3 border-bottom">
        <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem;">
            <i class="bi bi-pencil-square"></i>
        </div>
        <div>
            <h3 class="fs-5 fw-bold text-dark mb-0">Detalles del Nuevo Curso</h3>
            <span class="text-muted fs-7">Completa la ficha técnica para ofertar el curso a los estudiantes.</span>
        </div>
    </div>

    <!-- Section 1: Información General -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="nombre">Nombre del curso <span class="text-danger">*</span></label>
            <input type="text" id="nombre" name="nombre" class="form-control form-control-modern" 
                   placeholder="Ej. Desarrollo Web Full-Stack con Laravel" required>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="maestro">Docente Responsable <span class="text-danger">*</span></label>
            <select id="maestro" name="maestro" class="form-select form-select-modern" required>
                <option value="">-- Seleccionar Docente --</option>
                @foreach ($maestros as $maestro)
                    <option value="{{ $maestro->id }}">
                        {{ $maestro->nombre }} {{ $maestro->apellido_paterno }} {{ $maestro->apellido_materno }} ({{ $maestro->materia }})
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Section 2: Métricas y Fechas -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label-modern" for="costo">Costo (MXN) <span class="text-danger">*</span></label>
            <div class="input-group">
                <span class="input-group-text bg-light border-end-0 fw-bold text-muted">$</span>
                <input type="number" step="0.01" min="0" id="costo" name="costo" class="form-control form-control-modern border-start-0" 
                       placeholder="0.00" required>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label-modern" for="horas">Horas Totales <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" min="1" id="horas" name="horas" class="form-control form-control-modern border-end-0" 
                       placeholder="40" required>
                <span class="input-group-text bg-light border-start-0 text-muted fs-7">hrs</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label-modern" for="max_alumnos">Cupo Máximo <span class="text-danger">*</span></label>
            <div class="input-group">
                <input type="number" min="1" id="max_alumnos" name="max_alumnos" class="form-control form-control-modern border-end-0" 
                       placeholder="30" required>
                <span class="input-group-text bg-light border-start-0 text-muted fs-7">alumnos</span>
            </div>
        </div>

        <div class="col-12 col-sm-6 col-lg-3">
            <label class="form-label-modern" for="imagen">Imagen de Portada</label>
            <input type="file" id="imagen" name="imagen" accept="image/*" class="form-control form-control-modern">
        </div>
    </div>

    <!-- Section 3: Calendario -->
    <div class="row g-4 mb-4">
        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="fecha_inicio">Fecha de Inicio <span class="text-danger">*</span></label>
            <input type="date" id="fecha_inicio" name="fecha_inicio" class="form-control form-control-modern" required>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="fecha_fin">Fecha de Finalización <span class="text-danger">*</span></label>
            <input type="date" id="fecha_fin" name="fecha_fin" class="form-control form-control-modern" required>
        </div>
    </div>

    <!-- Section 4: Descripción y Objetivos -->
    <div class="row g-4">
        <div class="col-12">
            <label class="form-label-modern" for="desc">Descripción Resumida <span class="text-danger">*</span></label>
            <textarea id="desc" name="desc" class="form-control form-control-modern" rows="2" 
                      placeholder="Breve resumen del contenido y relevancia del curso..." required></textarea>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="objetivos">Objetivos de Aprendizaje <span class="text-danger">*</span></label>
            <textarea id="objetivos" name="objetivos" class="form-control form-control-modern" rows="3" 
                      placeholder="Competencias y metas que adquirirá el estudiante..." required></textarea>
        </div>

        <div class="col-12 col-md-6">
            <label class="form-label-modern" for="requisitos">Requisitos Previos <span class="text-danger">*</span></label>
            <textarea id="requisitos" name="requisitos" class="form-control form-control-modern" rows="3" 
                      placeholder="Conocimientos o herramientas necesarias para cursar..." required></textarea>
        </div>
    </div>
</div>
