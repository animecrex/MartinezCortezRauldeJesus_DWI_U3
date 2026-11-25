@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(16, 185, 129, 0.2); color: #d1fae5; border: 1px solid rgba(16, 185, 129, 0.4);">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem; color: #34d399;"></i>
                        SERVIDOR ONLINE
                    </span>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.14); color: #e2e8f0;">
                        Entorno: {{ strtoupper($diagnostico['runtime']['entorno']) }}
                    </span>
                </div>
                <h1 class="page-hero-title">Servidor de Aplicaciones 🖥️</h1>
                <p class="page-hero-subtitle mb-0">
                    Monitoreo en tiempo real del entorno Apache/PHP, núcleo Laravel, base de datos MariaDB, seguridad HTTP y Web Services de Curso+.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <button type="button" id="btnActualizarDiagnostico" class="btn-modern-primary">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Actualizar Diagnóstico</span>
                </button>
                <button type="button" id="btnLimpiarCache" class="btn-modern-outline" style="background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.3);">
                    <i class="bi bi-trash3"></i>
                    <span>Limpiar Caché</span>
                </button>
            </div>
        </div>

        <!-- KPI Summary Row -->
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Servidor Web & PHP</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-hdd-rack-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1" id="kpiPhp">PHP {{ $diagnostico['runtime']['php_version'] }}</div>
                    <div class="text-muted fs-8 text-truncate" title="{{ $diagnostico['servidor']['software'] }}">
                        {{ $diagnostico['servidor']['software'] }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Núcleo de Aplicación</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #fef2f2; color: #dc2626;">
                            <i class="bi bi-layers-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1">Laravel {{ $diagnostico['runtime']['laravel_version'] }}</div>
                    <div class="text-muted fs-8">
                        Memoria: <strong id="kpiMemoria">{{ $diagnostico['runtime']['memoria_usada_mb'] }} MB</strong> / {{ $diagnostico['runtime']['memoria_limite'] }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Base de Datos</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669;">
                            <i class="bi bi-database-fill-check fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-4 text-dark" id="kpiDbEstado">{{ $diagnostico['base_datos']['estado'] }}</span>
                        <span class="badge {{ $diagnostico['base_datos']['estado'] === 'ONLINE' ? 'bg-success' : 'bg-danger' }} fs-8" id="kpiDbLatencia">
                            {{ $diagnostico['base_datos']['latencia_ms'] }} ms
                        </span>
                    </div>
                    <div class="text-muted fs-8 text-truncate">
                        {{ $diagnostico['base_datos']['nombre'] }} · {{ $diagnostico['base_datos']['host'] }}:{{ $diagnostico['base_datos']['puerto'] }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Web Service Externo</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                            <i class="bi bi-globe2 fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-4 text-dark" id="kpiWsEstado">
                            {{ $diagnostico['web_services']['externo']['en_linea'] ? 'En Línea' : 'Fallback' }}
                        </span>
                    </div>
                    <div class="text-muted fs-8">
                        1 MXN ≈ <strong id="kpiTasaUsd">{{ $diagnostico['web_services']['externo']['tasa_usd'] ?? 'N/A' }}</strong> USD
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Grid: Server Info + Database Stats -->
        <div class="row g-5 mb-6">
            <!-- Left: Server & Runtime Specifications -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-cpu-fill text-primary fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Especificaciones del Servidor y Runtime</h3>
                                <span class="text-muted fs-8">Arquitectura activa que atiende las solicitudes HTTP.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-4">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">Servidor HTTP</td>
                                        <td class="text-dark fw-semibold text-end">{{ $diagnostico['servidor']['software'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Protocolo y Esquema</td>
                                        <td class="text-dark text-end">{{ $diagnostico['servidor']['protocolo'] }} ({{ $diagnostico['servidor']['esquema'] }})</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Host y Puerto</td>
                                        <td class="text-dark text-end"><code>{{ $diagnostico['servidor']['host'] }}:{{ $diagnostico['servidor']['puerto'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Sistema Operativo</td>
                                        <td class="text-dark text-end">{{ $diagnostico['servidor']['sistema_operativo'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Interfaz PHP (SAPI)</td>
                                        <td class="text-dark text-end"><code>{{ $diagnostico['runtime']['php_sapi'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Límites de Carga</td>
                                        <td class="text-dark text-end">Upload: <code>{{ $diagnostico['runtime']['upload_max'] }}</code> · POST: <code>{{ $diagnostico['runtime']['post_max'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Hora del Servidor</td>
                                        <td class="text-dark text-end" id="serverTime">{{ $diagnostico['servidor']['hora_servidor'] }} ({{ $diagnostico['servidor']['zona_horaria'] }})</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="fw-bold text-dark fs-7 mb-2">Extensiones PHP Verificadas</div>
                        <div class="d-flex flex-wrap gap-2">
                            @foreach($diagnostico['runtime']['extensiones'] as $ext => $cargada)
                                <span class="badge {{ $cargada ? 'bg-light-success text-success border border-success-subtle' : 'bg-light-danger text-danger border border-danger-subtle' }} px-3 py-2">
                                    <i class="bi {{ $cargada ? 'bi-check-circle-fill' : 'bi-x-circle-fill' }} me-1"></i>
                                    {{ $ext }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right: Database & Live Platform Metrics -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-database-fill text-success fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Motor de Base de Datos y Registros</h3>
                                <span class="text-muted fs-8">Conexión PDO activa con MariaDB / MySQL.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive mb-4">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">Motor / Versión</td>
                                        <td class="text-dark fw-semibold text-end">{{ $diagnostico['base_datos']['version'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Driver / Conexión</td>
                                        <td class="text-dark text-end"><code>{{ $diagnostico['base_datos']['driver'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Base de Datos Activa</td>
                                        <td class="text-dark text-end"><code>{{ $diagnostico['base_datos']['nombre'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Dirección del Servidor</td>
                                        <td class="text-dark text-end"><code>{{ $diagnostico['base_datos']['host'] }}:{{ $diagnostico['base_datos']['puerto'] }}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="fw-bold text-dark fs-7 mb-3">Volumen Actual en Base de Datos</div>
                        <div class="row g-3">
                            <div class="col-6">
                                <div class="p-3 rounded-3 border bg-light text-center">
                                    <div class="fs-2 fw-bolder text-primary" id="countCursos">{{ $diagnostico['base_datos']['conteos']['cursos'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Cursos Registrados</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 border bg-light text-center">
                                    <div class="fs-2 fw-bolder text-success" id="countMaestros">{{ $diagnostico['base_datos']['conteos']['maestros'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Maestros Activos</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 border bg-light text-center">
                                    <div class="fs-2 fw-bolder text-info" id="countUsuarios">{{ $diagnostico['base_datos']['conteos']['usuarios'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Usuarios Registrados</div>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="p-3 rounded-3 border bg-light text-center">
                                    <div class="fs-2 fw-bolder text-warning" id="countSuscripciones">{{ $diagnostico['base_datos']['conteos']['suscripciones'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Suscripciones Activas</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Grid: Security / Logs + Live API Tester -->
        <div class="row g-5">
            <!-- Security, Storage & Logs -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-lock-fill text-warning fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Seguridad, Sesiones y Bitácoras</h3>
                                <span class="text-muted fs-8">Endurecimiento perimetral en Apache, Laravel y canales de log.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-2 mb-4">
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <div class="text-muted fs-8">Sesiones y Caché</div>
                                    <div class="fw-bold text-dark fs-7 mt-1">
                                        Driver: <code>{{ $diagnostico['seguridad_y_almacenamiento']['session_driver'] }}</code> ({{ $diagnostico['seguridad_y_almacenamiento']['session_lifetime'] }} min)
                                    </div>
                                    <div class="text-muted fs-8 mt-1">
                                        HttpOnly: <strong>{{ $diagnostico['seguridad_y_almacenamiento']['session_http_only'] ? 'Sí' : 'No' }}</strong> · SameSite: <code>{{ $diagnostico['seguridad_y_almacenamiento']['session_same_site'] }}</code>
                                    </div>
                                </div>
                            </div>
                            <div class="col-sm-6">
                                <div class="p-3 rounded-3 border bg-light h-100">
                                    <div class="text-muted fs-8">Configuración Apache y Storage</div>
                                    <div class="mt-1 fs-8">
                                        <div>
                                            <i class="bi {{ $diagnostico['seguridad_y_almacenamiento']['htaccess_raiz'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                            <code>.htaccess</code> raíz activo
                                        </div>
                                        <div>
                                            <i class="bi {{ $diagnostico['seguridad_y_almacenamiento']['htaccess_public'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                            <code>public/.htaccess</code> endurecido
                                        </div>
                                        <div>
                                            <i class="bi {{ $diagnostico['seguridad_y_almacenamiento']['storage_enlazado'] ? 'bi-check-circle-fill text-success' : 'bi-x-circle-fill text-danger' }} me-1"></i>
                                            Enlace <code>public/storage</code>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="fw-bold text-dark fs-7 mb-2">Middlewares de Protección Activos</div>
                        <ul class="list-unstyled mb-4">
                            @foreach($diagnostico['seguridad_y_almacenamiento']['middlewares_activos'] as $mw)
                                <li class="d-flex align-items-center gap-2 mb-1 fs-8 text-dark">
                                    <i class="bi bi-shield-check text-success"></i>
                                    <span>{{ $mw }}</span>
                                </li>
                            @endforeach
                        </ul>

                        <div class="fw-bold text-dark fs-7 mb-2">Bitácoras del Servidor (<code>storage/logs/</code>)</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <thead>
                                    <tr class="text-muted fs-8">
                                        <th>Canal</th>
                                        <th>Archivo</th>
                                        <th class="text-end">Tamaño</th>
                                        <th class="text-end">Estado</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-8">
                                    @foreach($diagnostico['seguridad_y_almacenamiento']['logs'] as $canal => $log)
                                        <tr>
                                            <td class="fw-semibold text-uppercase">{{ $canal }}</td>
                                            <td><code>{{ $log['archivo'] }}</code></td>
                                            <td class="text-end">{{ $log['tamano_kb'] }} KB</td>
                                            <td class="text-end">
                                                <span class="badge {{ $log['activo'] ? 'bg-light-success text-success' : 'bg-light text-muted' }}">
                                                    {{ $log['activo'] ? 'Activo' : 'Listo' }}
                                                </span>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Live Web Services Tester -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-braces-asterisk text-info fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Consola de Pruebas de Web Services</h3>
                                <span class="text-muted fs-8">Verifica en vivo las respuestas JSON servidas por el servidor de aplicaciones.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="d-flex flex-wrap gap-2 mb-3">
                            @foreach($diagnostico['web_services']['endpoints_propios'] as $ep)
                                <button type="button"
                                        class="btn btn-sm btn-light-primary fw-semibold btn-test-endpoint"
                                        data-endpoint="{{ url($ep['ruta']) }}"
                                        title="{{ $ep['descripcion'] }}">
                                    <span class="badge bg-primary me-1">{{ $ep['metodo'] }}</span>
                                    {{ $ep['ruta'] }}
                                </button>
                            @endforeach
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-2">
                            <span class="text-muted fs-8 fw-semibold" id="apiTesterLabel">
                                Haz clic en cualquier endpoint para consultar el servidor en vivo:
                            </span>
                            <span class="badge bg-secondary fs-8" id="apiTesterStatus">En espera</span>
                        </div>

                        <pre id="apiTesterOutput"
                             class="p-3 rounded-3 mb-0"
                             style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.8rem; max-height: 310px; overflow: auto;">{
  "servidor": "Curso+ Application Server",
  "estado": "Listo para probar endpoints /api/v1"
}</pre>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('javascript')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
            const btnActualizar = document.getElementById('btnActualizarDiagnostico');
            const btnLimpiarCache = document.getElementById('btnLimpiarCache');
            const apiOutput = document.getElementById('apiTesterOutput');
            const apiLabel = document.getElementById('apiTesterLabel');
            const apiStatus = document.getElementById('apiTesterStatus');

            // 1. Actualizar diagnóstico en vivo vía Fetch
            btnActualizar?.addEventListener('click', async function () {
                btnActualizar.disabled = true;
                try {
                    const res = await fetch("{{ route('servidor.estado') }}", {
                        headers: { 'Accept': 'application/json' }
                    });
                    const payload = await res.json();
                    if (payload.exito && payload.datos) {
                        const d = payload.datos;
                        document.getElementById('kpiMemoria').textContent = d.runtime.memoria_usada_mb + ' MB';
                        document.getElementById('kpiDbEstado').textContent = d.base_datos.estado;
                        document.getElementById('kpiDbLatencia').textContent = d.base_datos.latencia_ms + ' ms';
                        document.getElementById('kpiWsEstado').textContent = d.web_services.externo.en_linea ? 'En Línea' : 'Fallback';
                        document.getElementById('kpiTasaUsd').textContent = d.web_services.externo.tasa_usd ?? 'N/A';
                        document.getElementById('serverTime').textContent = d.servidor.hora_servidor + ' (' + d.servidor.zona_horaria + ')';
                        document.getElementById('countCursos').textContent = d.base_datos.conteos.cursos;
                        document.getElementById('countMaestros').textContent = d.base_datos.conteos.maestros;
                        document.getElementById('countUsuarios').textContent = d.base_datos.conteos.usuarios;
                        document.getElementById('countSuscripciones').textContent = d.base_datos.conteos.suscripciones;

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Diagnóstico actualizado en tiempo real',
                            showConfirmButton: false,
                            timer: 2200
                        });
                    }
                } catch (err) {
                    Swal.fire('Error', 'No se pudo actualizar el estado del servidor.', 'error');
                } finally {
                    btnActualizar.disabled = false;
                }
            });

            // 2. Limpiar caché del servidor (POST con CSRF)
            btnLimpiarCache?.addEventListener('click', async function () {
                btnLimpiarCache.disabled = true;
                try {
                    const res = await fetch("{{ route('servidor.limpiar-cache') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const payload = await res.json();
                    if (payload.exito) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Caché Limpiada',
                            text: payload.mensaje,
                            timer: 2500,
                            showConfirmButton: false
                        });
                    }
                } catch (err) {
                    Swal.fire('Error', 'No se pudo limpiar la caché del servidor.', 'error');
                } finally {
                    btnLimpiarCache.disabled = false;
                }
            });

            // 3. Consola rápida de Web Services
            document.querySelectorAll('.btn-test-endpoint').forEach(btn => {
                btn.addEventListener('click', async function () {
                    const url = this.getAttribute('data-endpoint');
                    apiLabel.textContent = 'Consultando: ' + url;
                    apiStatus.textContent = 'Cargando...';
                    apiStatus.className = 'badge bg-warning text-dark fs-8';
                    apiOutput.textContent = 'Consultando servidor de aplicaciones...';

                    try {
                        const start = performance.now();
                        const res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                        const elapsed = Math.round(performance.now() - start);
                        const json = await res.json();

                        apiStatus.textContent = 'HTTP ' + res.status + ' · ' + elapsed + ' ms';
                        apiStatus.className = res.ok ? 'badge bg-success fs-8' : 'badge bg-danger fs-8';
                        apiOutput.textContent = JSON.stringify(json, null, 2);
                    } catch (err) {
                        apiStatus.textContent = 'Error de red';
                        apiStatus.className = 'badge bg-danger fs-8';
                        apiOutput.textContent = JSON.stringify({ error: 'No se pudo conectar con el endpoint.' }, null, 2);
                    }
                });
            });
        });
    </script>
@endsection
