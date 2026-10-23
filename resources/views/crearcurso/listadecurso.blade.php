<div class="modern-card">
    <div class="modern-card-header d-flex flex-column flex-sm-row align-items-start align-items-sm-center justify-content-between gap-3">
        <div class="d-flex align-items-center gap-2">
            <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem; background: var(--brand-secondary);">
                <i class="bi bi-collection"></i>
            </div>
            <div>
                <h3 class="modern-card-title">Cursos Creados</h3>
                <span class="text-muted fs-8">Lista de cursos creados bajo tu administración.</span>
            </div>
        </div>

        <div class="input-group" style="max-width: 300px;">
            <span class="input-group-text bg-light border-end-0">
                <i class="bi bi-search text-muted"></i>
            </span>
            <input type="text" id="buscador" class="form-control form-control-modern border-start-0" 
                   placeholder="Filtrar cursos...">
        </div>
    </div>

    <div class="p-3 p-md-5">
        <div class="table-responsive">
            <table id="dt_search" class="table-modern w-100">
                <thead>
                    <tr>
                        <th style="min-width: 220px;">Curso</th>
                        <th>Docente</th>
                        <th>Descripción</th>
                        <th>Inversión</th>
                        <th class="text-center" style="width: 140px;">Acciones</th>
                    </tr>
                </thead>
                <tbody id="tbody-cursos">
                    <!-- Populated by DataTables in curso.js -->
                </tbody>
            </table>
        </div>
    </div>
</div>
