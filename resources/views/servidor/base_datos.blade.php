@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <div class="d-flex align-items-center gap-2 mb-2">
                    <span id="badgeEstadoHero"
                          class="badge rounded-pill px-3 py-2"
                          style="background: {{ $bd['conexion']['estado'] === 'ONLINE' ? 'rgba(16, 185, 129, 0.2)' : 'rgba(239, 68, 68, 0.25)' }}; color: {{ $bd['conexion']['estado'] === 'ONLINE' ? '#d1fae5' : '#fee2e2' }}; border: 1px solid {{ $bd['conexion']['estado'] === 'ONLINE' ? 'rgba(16, 185, 129, 0.4)' : 'rgba(239, 68, 68, 0.5)' }};">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem; color: {{ $bd['conexion']['estado'] === 'ONLINE' ? '#34d399' : '#f87171' }};"></i>
                        {{ $bd['conexion']['estado'] === 'ONLINE' ? 'BASE DE DATOS ONLINE' : 'SIN CONEXIÓN' }}
                    </span>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.14); color: #e2e8f0;">
                        BD: {{ $bd['conexion']['nombre'] }}
                    </span>
                </div>
                <h1 class="page-hero-title">Servidor de Base de Datos 🗄️</h1>
                <p class="page-hero-subtitle mb-0">
                    Monitoreo en tiempo real del motor MariaDB / MySQL, latencia PDO, inventario de tablas, rendimiento y seguridad de persistencia de Curso+.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <button type="button" id="btnActualizarBd" class="btn-modern-primary">
                    <i class="bi bi-arrow-clockwise"></i>
                    <span>Actualizar Métricas</span>
                </button>
                <button type="button" id="btnProbarConexion" class="btn-modern-outline" style="background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.3);">
                    <i class="bi bi-plug-fill"></i>
                    <span>Probar Conexión PDO</span>
                </button>
            </div>
        </div>

        <!-- KPI Summary Row -->
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Motor & Versión</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669;">
                            <i class="bi bi-database-fill-check fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1 text-truncate" id="kpiBdVersion" title="{{ $bd['conexion']['version'] }}">
                        {{ $bd['conexion']['version'] }}
                    </div>
                    <div class="text-muted fs-8 text-truncate">
                        Driver: <strong>{{ $bd['conexion']['driver'] }}</strong> · Motor: <strong>{{ $bd['conexion']['motor_defecto'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Conexión & Latencia</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-speedometer2 fs-5"></i>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2 mb-1">
                        <span class="fw-bold fs-4 text-dark" id="kpiBdEstado">{{ $bd['conexion']['estado'] }}</span>
                        <span class="badge {{ $bd['conexion']['estado'] === 'ONLINE' ? 'bg-success' : 'bg-danger' }} fs-8" id="kpiBdLatencia">
                            {{ $bd['conexion']['latencia_ms'] }} ms
                        </span>
                    </div>
                    <div class="text-muted fs-8">
                        <code>{{ $bd['conexion']['host'] }}:{{ $bd['conexion']['puerto'] }}</code>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Tablas & Almacenamiento</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                            <i class="bi bi-table fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1">
                        <span id="kpiTotalTablas">{{ $bd['rendimiento']['total_tablas'] }}</span> tablas
                    </div>
                    <div class="text-muted fs-8">
                        Tamaño total: <strong id="kpiTamanoTotal">{{ $bd['rendimiento']['tamano_total_kb'] }} KB</strong> ({{ $bd['rendimiento']['tamano_total_mb'] }} MB)
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Actividad del Servidor</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #fffbeb; color: #d97706;">
                            <i class="bi bi-activity fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1" id="kpiUptime">{{ $bd['rendimiento']['uptime'] }}</div>
                    <div class="text-muted fs-8">
                        Conexiones: <strong id="kpiConexiones">{{ $bd['rendimiento']['conexiones_activas'] }}</strong> / {{ $bd['rendimiento']['max_connections'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Row: Server Parameters + Domain Entities -->
        <div class="row g-5 mb-6">
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-sliders text-primary fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Parámetros del Servidor de Base de Datos</h3>
                                <span class="text-muted fs-8">Configuración activa de conexión y motor relacional.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">Distribución</td>
                                        <td class="text-dark fw-semibold text-end">{{ $bd['conexion']['distribucion'] }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Versión del Motor</td>
                                        <td class="text-dark text-end"><code>{{ $bd['conexion']['version'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Base de Datos Activa</td>
                                        <td class="text-dark text-end"><code>{{ $bd['conexion']['nombre'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Host y Puerto TCP</td>
                                        <td class="text-dark text-end"><code>{{ $bd['conexion']['host'] }}:{{ $bd['conexion']['puerto'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Motor de Almacenamiento</td>
                                        <td class="text-dark text-end"><span class="badge bg-light-primary text-primary">{{ $bd['conexion']['motor_defecto'] }}</span></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Charset / Collation</td>
                                        <td class="text-dark text-end"><code>{{ $bd['conexion']['charset'] }}</code> / <code>{{ $bd['conexion']['collation'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Consultas Procesadas</td>
                                        <td class="text-dark fw-bold text-end" id="metricConsultas">{{ number_format($bd['rendimiento']['consultas_totales']) }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Última Verificación</td>
                                        <td class="text-dark text-end" id="metricHoraConsulta">{{ $bd['hora_consulta'] }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-diagram-3-fill text-success fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Entidades Principales de Curso+</h3>
                                <span class="text-muted fs-8">Registros persistidos actualmente en la base de datos.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-3 mb-4">
                            <div class="col-6 col-sm-4">
                                <div class="p-3 rounded-3 border bg-light text-center h-100">
                                    <i class="bi bi-book-half text-primary fs-4 mb-1 d-block"></i>
                                    <div class="fs-2 fw-bolder text-primary" id="bdCountCursos">{{ $bd['conteos']['cursos'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Cursos</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 rounded-3 border bg-light text-center h-100">
                                    <i class="bi bi-person-badge-fill text-success fs-4 mb-1 d-block"></i>
                                    <div class="fs-2 fw-bolder text-success" id="bdCountMaestros">{{ $bd['conteos']['maestros'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Maestros</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-4">
                                <div class="p-3 rounded-3 border bg-light text-center h-100">
                                    <i class="bi bi-people-fill text-info fs-4 mb-1 d-block"></i>
                                    <div class="fs-2 fw-bolder text-info" id="bdCountUsuarios">{{ $bd['conteos']['usuarios'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Usuarios</div>
                                </div>
                            </div>
                            <div class="col-6 col-sm-6">
                                <div class="p-3 rounded-3 border bg-light text-center h-100">
                                    <i class="bi bi-collection-play-fill text-warning fs-4 mb-1 d-block"></i>
                                    <div class="fs-2 fw-bolder text-warning" id="bdCountSuscripciones">{{ $bd['conteos']['suscripciones'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Suscripciones</div>
                                </div>
                            </div>
                            <div class="col-12 col-sm-6">
                                <div class="p-3 rounded-3 border bg-light text-center h-100">
                                    <i class="bi bi-credit-card-2-front-fill text-danger fs-4 mb-1 d-block"></i>
                                    <div class="fs-2 fw-bolder text-danger" id="bdCountTarjetas">{{ $bd['conteos']['tarjetas'] }}</div>
                                    <div class="text-muted fs-8 fw-semibold">Tarjetas Registradas</div>
                                </div>
                            </div>
                        </div>

                        <div class="fw-bold text-dark fs-7 mb-2">Seguridad en la Capa de Datos</div>
                        <ul class="list-unstyled mb-0">
                            @foreach($bd['seguridad_bd'] as $item)
                                <li class="d-flex align-items-start gap-2 mb-1 fs-8 text-dark">
                                    <i class="bi bi-shield-check text-success mt-1"></i>
                                    <span>{{ $item }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Live Tables Explorer + Migrations -->
        <div class="row g-5">
            <div class="col-lg-8">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-table text-info fs-4"></i>
                                <div>
                                    <h3 class="modern-card-title mb-0">Explorador de Tablas en Vivo</h3>
                                    <span class="text-muted fs-8">Metadatos consultados desde <code>information_schema.TABLES</code> para <code>{{ $bd['conexion']['nombre'] }}</code>.</span>
                                </div>
                            </div>
                            <span class="badge bg-light-primary text-primary px-3 py-2 fs-8">
                                {{ $bd['rendimiento']['total_tablas'] }} tablas
                            </span>
                        </div>
                    </div>
                    <div class="p-4">
                        @if(count($bd['tablas']) > 0)
                            <div class="table-responsive">
                                <table class="table table-hover table-sm align-middle mb-0">
                                    <thead>
                                        <tr class="text-muted fs-8 border-bottom">
                                            <th>Tabla</th>
                                            <th>Motor</th>
                                            <th class="text-end">Filas aprox.</th>
                                            <th class="text-end">Tamaño (KB)</th>
                                            <th>Collation</th>
                                        </tr>
                                    </thead>
                                    <tbody class="fs-8" id="tablaExploradorBody">
                                        @foreach($bd['tablas'] as $tabla)
                                            <tr>
                                                <td class="fw-bold text-dark"><code>{{ $tabla['nombre'] }}</code></td>
                                                <td><span class="badge bg-light text-dark border">{{ $tabla['motor'] }}</span></td>
                                                <td class="text-end fw-semibold">{{ number_format($tabla['filas']) }}</td>
                                                <td class="text-end">{{ number_format($tabla['tamano_kb'], 2) }} KB</td>
                                                <td class="text-muted">{{ $tabla['collation'] }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        @else
                            <div class="alert alert-warning mb-0 d-flex align-items-center gap-2">
                                <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                                <span>No se pudieron listar las tablas. Asegúrate de que el servicio <strong>MySQL / MariaDB</strong> esté iniciado en XAMPP.</span>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-clock-history text-warning fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Migraciones Recientes</h3>
                                <span class="text-muted fs-8">Control de esquema en Laravel.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        @if(count($bd['migraciones']) > 0)
                            <ul class="list-unstyled mb-0">
                                @foreach($bd['migraciones'] as $mig)
                                    <li class="d-flex align-items-center justify-content-between py-2 border-bottom fs-8 gap-2">
                                        <span class="text-dark text-truncate" title="{{ $mig['nombre'] }}">
                                            <i class="bi bi-check2-circle text-success me-1"></i>
                                            <code>{{ $mig['nombre'] }}</code>
                                        </span>
                                        <span class="badge bg-light text-muted border">Lote {{ $mig['lote'] }}</span>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <p class="text-muted fs-8 mb-0">Sin registro de migraciones disponible en este momento.</p>
                        @endif
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
            const btnActualizar = document.getElementById('btnActualizarBd');
            const btnProbar = document.getElementById('btnProbarConexion');

            // 1. Actualizar métricas en vivo
            btnActualizar?.addEventListener('click', async function () {
                btnActualizar.disabled = true;
                try {
                    const res = await fetch("{{ route('servidor.bd.estado') }}", {
                        headers: { 'Accept': 'application/json' }
                    });
                    const payload = await res.json();
                    if (payload.exito && payload.datos) {
                        const d = payload.datos;
                        document.getElementById('kpiBdVersion').textContent = d.conexion.version;
                        document.getElementById('kpiBdEstado').textContent = d.conexion.estado;
                        document.getElementById('kpiBdLatencia').textContent = d.conexion.latencia_ms + ' ms';
                        document.getElementById('kpiTotalTablas').textContent = d.rendimiento.total_tablas;
                        document.getElementById('kpiTamanoTotal').textContent = d.rendimiento.tamano_total_kb + ' KB';
                        document.getElementById('kpiUptime').textContent = d.rendimiento.uptime;
                        document.getElementById('kpiConexiones').textContent = d.rendimiento.conexiones_activas;
                        document.getElementById('metricConsultas').textContent = Number(d.rendimiento.consultas_totales).toLocaleString();
                        document.getElementById('metricHoraConsulta').textContent = d.hora_consulta;
                        document.getElementById('bdCountCursos').textContent = d.conteos.cursos;
                        document.getElementById('bdCountMaestros').textContent = d.conteos.maestros;
                        document.getElementById('bdCountUsuarios').textContent = d.conteos.usuarios;
                        document.getElementById('bdCountSuscripciones').textContent = d.conteos.suscripciones;
                        document.getElementById('bdCountTarjetas').textContent = d.conteos.tarjetas;

                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Métricas de BD actualizadas',
                            showConfirmButton: false,
                            timer: 2200
                        });
                    }
                } catch (err) {
                    Swal.fire('Error', 'No se pudieron actualizar las métricas de la base de datos.', 'error');
                } finally {
                    btnActualizar.disabled = false;
                }
            });

            // 2. Probar conexión PDO en tiempo real (POST + CSRF)
            btnProbar?.addEventListener('click', async function () {
                btnProbar.disabled = true;
                try {
                    const res = await fetch("{{ route('servidor.bd.probar') }}", {
                        method: 'POST',
                        headers: {
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        }
                    });
                    const payload = await res.json();
                    if (payload.exito) {
                        document.getElementById('kpiBdEstado').textContent = payload.datos.estado;
                        document.getElementById('kpiBdLatencia').textContent = payload.datos.latencia_ms + ' ms';
                        Swal.fire({
                            icon: 'success',
                            title: 'Conexión PDO Exitosa',
                            html: `Servidor <strong>${payload.datos.version}</strong><br>Base de datos: <code>${payload.datos.base_datos}</code><br>Latencia: <strong>${payload.datos.latencia_ms} ms</strong>`,
                            timer: 3200,
                            showConfirmButton: false
                        });
                    } else {
                        Swal.fire('Sin Conexión', payload.mensaje, 'warning');
                    }
                } catch (err) {
                    Swal.fire('Error', 'Error al ejecutar la prueba de conexión PDO.', 'error');
                } finally {
                    btnProbar.disabled = false;
                }
            });
        });
    </script>
@endsection
