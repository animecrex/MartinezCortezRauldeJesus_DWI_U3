<div class="row g-6">
    <!-- Left Column: Form Card -->
    <div class="col-lg-5">
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem;">
                        <i class="bi bi-person-plus-fill"></i>
                    </div>
                    <div>
                        <h3 class="modern-card-title">Registrar Maestro</h3>
                        <span class="text-muted fs-8">Ingresa los datos personales del docente.</span>
                    </div>
                </div>
            </div>

            <div class="p-4 p-md-5">
                <!-- Nombre -->
                <div class="mb-4">
                    <label class="form-label-modern" for="nombre">Nombre(s) <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-person text-muted"></i></span>
                        <input type="text" id="nombre" name="nombre" value="{{ old('nombre') }}" 
                               class="form-control form-control-modern border-start-0 @error('nombre') is-invalid @enderror" 
                               placeholder="Ej. Roberto" required>
                    </div>
                    @error('nombre')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <!-- Apellidos Row -->
                <div class="row g-3 mb-4">
                    <div class="col-6">
                        <label class="form-label-modern" for="apellido_paterno">Apellido Paterno <span class="text-danger">*</span></label>
                        <input type="text" id="apellido_paterno" name="apellido_paterno" value="{{ old('apellido_paterno') }}" 
                               class="form-control form-control-modern @error('apellido_paterno') is-invalid @enderror" 
                               placeholder="Paterno" required>
                        @error('apellido_paterno')
                            <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>

                    <div class="col-6">
                        <label class="form-label-modern" for="apellido_materno">Apellido Materno</label>
                        <input type="text" id="apellido_materno" name="apellido_materno" value="{{ old('apellido_materno') }}" 
                               class="form-control form-control-modern @error('apellido_materno') is-invalid @enderror" 
                               placeholder="Materno">
                        @error('apellido_materno')
                            <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                        @enderror
                    </div>
                </div>

                <!-- Correo Electrónico -->
                <div class="mb-4">
                    <label class="form-label-modern" for="correo">Correo Electrónico <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-envelope text-muted"></i></span>
                        <input type="email" id="correo" name="correo" value="{{ old('correo') }}" 
                               class="form-control form-control-modern border-start-0 @error('correo') is-invalid @enderror" 
                               placeholder="docente@ejemplo.com" required>
                    </div>
                    @error('correo')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <!-- Materia -->
                <div class="mb-4">
                    <label class="form-label-modern" for="materia">Especialidad / Materia <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text bg-light border-end-0"><i class="bi bi-book text-muted"></i></span>
                        <input type="text" id="materia" name="materia" value="{{ old('materia') }}" 
                               class="form-control form-control-modern border-start-0 @error('materia') is-invalid @enderror" 
                               placeholder="Ej. Programación Orientada a Objetos" required>
                    </div>
                    @error('materia')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <!-- Turno -->
                <div class="mb-5">
                    <label class="form-label-modern" for="turno">Turno Escolar <span class="text-danger">*</span></label>
                    <select id="turno" name="turno" class="form-select form-select-modern @error('turno') is-invalid @enderror" required>
                        <option value="">-- Seleccionar Turno --</option>
                        <option value="Matutino" {{ old('turno') == 'Matutino' ? 'selected' : '' }}>☀️ Matutino</option>
                        <option value="Vespertino" {{ old('turno') == 'Vespertino' ? 'selected' : '' }}>🌙 Vespertino</option>
                    </select>
                    @error('turno')
                        <small class="text-danger mt-1 d-block"><i class="bi bi-exclamation-circle me-1"></i>{{ $message }}</small>
                    @enderror
                </div>

                <!-- Botón de Envío -->
                <button type="submit" id="btnAgregarMaestro" class="btn-modern-primary w-100 justify-content-center py-3">
                    <i class="bi bi-check-circle-fill"></i>
                    <span>Guardar y Registrar Maestro</span>
                </button>
            </div>
        </div>
    </div>

    <!-- Right Column: Teachers Directory Table -->
    <div class="col-lg-7">
        <div class="modern-card">
            <div class="modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <div class="user-avatar-circle" style="width: 36px; height: 36px; font-size: 1rem; background: var(--brand-secondary);">
                        <i class="bi bi-person-lines-fill"></i>
                    </div>
                    <div>
                        <h3 class="modern-card-title">Directorio Docente</h3>
                        <span class="text-muted fs-8">Lista actualizada de profesores habilitados.</span>
                    </div>
                </div>
            </div>

            <div class="p-3 p-md-4">
                <div class="table-responsive">
                    <table class="table-modern w-100">
                        <thead>
                            <tr>
                                <th>Docente</th>
                                <th>Materia</th>
                                <th>Turno</th>
                            </tr>
                        </thead>
                        <tbody id="tablaMaestros">
                            @forelse($maestros as $maestro)
                                <tr>
                                    <td>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="user-avatar-circle" style="width: 38px; height: 38px; font-size: 0.95rem;">
                                                {{ strtoupper(substr($maestro->nombre, 0, 1)) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-dark fs-6">{{ $maestro->nombre }} {{ $maestro->apellido_paterno }} {{ $maestro->apellido_materno }}</div>
                                                <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $maestro->correo }}</small>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge-modern-primary">
                                            <i class="bi bi-book-half me-1"></i>{{ $maestro->materia }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($maestro->turno === 'Matutino')
                                            <span class="badge-modern-warning">
                                                ☀️ Matutino
                                            </span>
                                        @else
                                            <span class="badge-modern-primary" style="background: #f1f5f9; color: #475569; border-color: #cbd5e1;">
                                                🌙 Vespertino
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="text-center py-5 text-muted">
                                        <i class="bi bi-people fs-2 d-block mb-2 text-muted"></i>
                                        No hay maestros registrados actualmente.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>