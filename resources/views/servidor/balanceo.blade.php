@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span id="badgeClusterEstado"
                          class="badge rounded-pill px-3 py-2"
                          style="background: {{ $estado['resumen']['nodos_online'] === $estado['resumen']['nodos_totales'] ? 'rgba(16, 185, 129, 0.2)' : ($estado['resumen']['nodos_online'] > 0 ? 'rgba(245, 158, 11, 0.25)' : 'rgba(239, 68, 68, 0.25)') }}; color: #fff; border: 1px solid rgba(255,255,255,0.35);">
                        <i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i>
                        CLÚSTER {{ $estado['resumen']['estado_cluster'] }}
                    </span>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.14); color: #e2e8f0;" id="badgeAlgoritmoHero">
                        Algoritmo: {{ $estado['resumen']['algoritmo_nombre'] }}
                    </span>
                </div>
                <h1 class="page-hero-title">Balanceo de Cargas ⚖️</h1>
                <p class="page-hero-subtitle mb-0">
                    Distribución inteligente de tráfico HTTP entre nodos de aplicación, control de algoritmos, failover automático por Health-Check y configuración de proxy inverso.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <button type="button" id="btnDespacharUno" class="btn-modern-primary">
                    <i class="bi bi-send-fill"></i>
                    <span>Despachar 1 Petición</span>
                </button>
                <button type="button" id="btnDespacharRafaga" class="btn-modern-outline" style="background: rgba(255,255,255,0.16); color: #fff; border-color: rgba(255,255,255,0.35);">
                    <i class="bi bi-lightning-charge-fill"></i>
                    <span>Ráfaga (10)</span>
                </button>
                <button type="button" id="btnReiniciarCluster" class="btn-modern-outline" style="background: rgba(255,255,255,0.08); color: #fff; border-color: rgba(255,255,255,0.25);">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Reiniciar</span>
                </button>
            </div>
        </div>

        <!-- KPI Row -->
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Nodos Saludables</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669;">
                            <i class="bi bi-hdd-network-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-3 text-dark mb-1" id="kpiNodosOnline">
                        {{ $estado['resumen']['nodos_online'] }} / {{ $estado['resumen']['nodos_totales'] }}
                    </div>
                    <div class="text-muted fs-8" id="kpiEstadoCluster">{{ $estado['resumen']['estado_cluster'] }}</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Peticiones Balanceadas</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-arrow-left-right fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-3 text-dark mb-1" id="kpiTotalPeticiones">
                        {{ $estado['total_peticiones'] }}
                    </div>
                    <div class="text-muted fs-8">Solicitudes enrutadas en sesión actual</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Latencia Promedio</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                            <i class="bi bi-speedometer2 fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-3 text-dark mb-1" id="kpiLatenciaPromedio">
                        {{ $estado['resumen']['latencia_promedio_ms'] }} ms
                    </div>
                    <div class="text-muted fs-8">Respuesta media de nodos ONLINE</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Algoritmo de Balanceo</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-diagram-2-fill fs-5"></i>
                        </div>
                    </div>
                    <select id="selectAlgoritmo" class="form-select form-select-sm fw-bold mb-1">
                        @foreach($estado['algoritmos_disponibles'] as $clave => $info)
                            <option value="{{ $clave }}" {{ $estado['algoritmo'] === $clave ? 'selected' : '' }}>
                                {{ $info['nombre'] }}
                            </option>
                        @endforeach
                    </select>
                    <div class="text-muted fs-8 text-truncate" id="descAlgoritmo" title="{{ $estado['resumen']['algoritmo_descripcion'] }}">
                        {{ $estado['resumen']['algoritmo_descripcion'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Nodes Pool + Dispatch Controls -->
        <div class="modern-card mb-6">
            <div class="modern-card-header">
                <div class="d-flex flex-wrap align-items-center justify-content-between w-100 gap-3">
                    <div class="d-flex align-items-center gap-2">
                        <i class="bi bi-diagram-3-fill text-primary fs-4"></i>
                        <div>
                            <h3 class="modern-card-title mb-0">Clúster de Nodos Backend & Failover Automático</h3>
                            <span class="text-muted fs-8">Desactiva cualquier nodo para comprobar cómo el balanceador redirige el tráfico a los nodos saludables.</span>
                        </div>
                    </div>
                    <div class="d-flex align-items-center gap-2">
                        <label for="selectRutaPrueba" class="text-muted fs-8 fw-semibold mb-0">Ruta simulada:</label>
                        <select id="selectRutaPrueba" class="form-select form-select-sm" style="width: auto;">
                            <option value="/api/v1/cursos">GET /api/v1/cursos</option>
                            <option value="/api/v1/health">GET /api/v1/health</option>
                            <option value="/api/v1/maestros">GET /api/v1/maestros</option>
                            <option value="/api/v1/tipo-cambio">GET /api/v1/tipo-cambio</option>
                            <option value="/curso">GET /curso</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="row g-4" id="nodosContainer">
                    @foreach($estado['nodos'] as $nodo)
                        @php $online = $nodo['estado'] === 'ONLINE'; @endphp
                        <div class="col-md-4">
                            <div class="p-4 rounded-3 border h-100 {{ $online ? 'bg-white' : 'bg-light opacity-75' }}" style="border-width: 1.5px !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <span class="fw-bold text-dark fs-6">{{ $nodo['id'] }}</span>
                                        <span class="badge bg-light text-muted border ms-1 fs-8">Peso {{ $nodo['peso'] }}</span>
                                    </div>
                                    <span class="badge {{ $online ? 'bg-success' : 'bg-danger' }} px-2 py-1 fs-8">
                                        {{ $nodo['estado'] }}
                                    </span>
                                </div>
                                <div class="text-muted fs-8 mb-3">
                                    {{ $nodo['nombre'] }} · <code>{{ $nodo['host'] }}:{{ $nodo['puerto'] }}</code>
                                </div>

                                <div class="d-flex justify-content-between fs-8 mb-1">
                                    <span class="text-muted">Distribución de carga</span>
                                    <strong class="text-dark">{{ $nodo['peticiones'] }} req ({{ $nodo['porcentaje'] }}%)</strong>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar {{ $online ? 'bg-primary' : 'bg-secondary' }}" role="progressbar" style="width: {{ $nodo['porcentaje'] }}%;"></div>
                                </div>

                                <div class="row g-2 text-center mb-3 fs-8">
                                    <div class="col-6">
                                        <div class="p-2 rounded bg-light border">
                                            <div class="text-muted">Conexiones</div>
                                            <div class="fw-bold text-dark fs-6">{{ $nodo['conexiones_activas'] }}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded bg-light border">
                                            <div class="text-muted">Latencia</div>
                                            <div class="fw-bold text-dark fs-6">{{ $online ? $nodo['latencia_ms'] . ' ms' : '—' }}</div>
                                        </div>
                                    </div>
                                </div>

                                <div class="text-muted fs-8 mb-3 text-truncate" title="{{ $nodo['ultimo_evento'] }}">
                                    <i class="bi bi-clock me-1"></i>{{ $nodo['ultimo_evento'] }}
                                </div>

                                <button type="button"
                                        class="btn btn-sm w-100 fw-semibold btn-toggle-nodo {{ $online ? 'btn-outline-danger' : 'btn-success' }}"
                                        data-nodo-id="{{ $nodo['id'] }}">
                                    <i class="bi {{ $online ? 'bi-power' : 'bi-check-circle-fill' }} me-1"></i>
                                    {{ $online ? 'Simular Caída (OFFLINE)' : 'Restaurar Nodo (ONLINE)' }}
                                </button>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Bottom Grid: Dispatch Log + Proxy & Production Load Balancer Config -->
        <div class="row g-5">
            <!-- Dispatch History -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-list-check text-info fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Bitácora de Enrutamiento en Vivo</h3>
                                <span class="text-muted fs-8">Últimas 12 solicitudes distribuidas por el balanceador.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive">
                            <table class="table table-sm table-hover align-middle mb-0">
                                <thead>
                                    <tr class="text-muted fs-8 border-bottom">
                                        <th>ID</th>
                                        <th>Hora</th>
                                        <th>Ruta</th>
                                        <th>Nodo Destino</th>
                                        <th class="text-end">Latencia</th>
                                    </tr>
                                </thead>
                                <tbody class="fs-8" id="historialBody">
                                    @forelse($estado['historial'] as $reg)
                                        <tr>
                                            <td class="fw-bold"><code>{{ $reg['id'] }}</code></td>
                                            <td class="text-muted">{{ $reg['hora'] }}</td>
                                            <td><code>{{ $reg['ruta'] }}</code></td>
                                            <td><span class="badge bg-light-primary text-primary">{{ $reg['nodo_id'] }}</span></td>
                                            <td class="text-end fw-semibold text-success">{{ $reg['latencia_ms'] }} ms</td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="5" class="text-center text-muted py-4">
                                                Haz clic en <strong>Despachar 1 Petición</strong> o <strong>Ráfaga (10)</strong> para observar el balanceo en tiempo real.
                                            </td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Real Laravel Readiness & Apache/Nginx Config -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-shield-check text-success fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Configuración de Proxy Inverso & Arquitectura L7</h3>
                                <span class="text-muted fs-8">Integración de Curso+ con Apache <code>mod_proxy_balancer</code> / Nginx y <code>TrustProxies</code>.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle mb-0 fs-8">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">Middleware Proxy</td>
                                        <td class="text-dark text-end"><code>{{ $estado['configuracion_laravel']['trust_proxies'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">IP Cliente / Forwarded</td>
                                        <td class="text-dark text-end"><code>{{ $estado['configuracion_laravel']['client_ip'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Endpoint Health-Check</td>
                                        <td class="text-dark text-end"><code>GET {{ $estado['configuracion_laravel']['health_endpoint'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Drivers Sesión / Caché</td>
                                        <td class="text-dark text-end">
                                            <code>{{ $estado['configuracion_laravel']['session_driver'] }}</code> / <code>{{ $estado['configuracion_laravel']['cache_driver'] }}</code>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="alert alert-info py-2 px-3 fs-8 mb-3">
                            <i class="bi bi-info-circle-fill me-1"></i>
                            {{ $estado['configuracion_laravel']['recomendacion_produccion'] }}
                        </div>

                        <div class="fw-bold text-dark fs-8 mb-2">Ejemplo Apache <code>mod_proxy_balancer</code> (XAMPP / Producción):</div>
                        <pre class="p-3 rounded-3 mb-0" style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.76rem; overflow: auto;">&lt;Proxy "balancer://cursoplus_cluster"&gt;
    BalancerMember http://127.0.0.1:8000 loadfactor=3
    BalancerMember http://127.0.0.1:8001 loadfactor=2
    BalancerMember http://127.0.0.1:8002 loadfactor=1 status=+H
    ProxySet lbmethod=byrequests
&lt;/Proxy&gt;
ProxyPass "/" "balancer://cursoplus_cluster/"
ProxyPassReverse "/" "balancer://cursoplus_cluster/"</pre>
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
            const toggleBaseUrl = "{{ url('/balanceo-cargas/nodo') }}";

            function renderEstado(d) {
                const r = d.resumen;
                document.getElementById('kpiNodosOnline').textContent = `${r.nodos_online} / ${r.nodos_totales}`;
                document.getElementById('kpiEstadoCluster').textContent = r.estado_cluster;
                document.getElementById('kpiTotalPeticiones').textContent = d.total_peticiones;
                document.getElementById('kpiLatenciaPromedio').textContent = `${r.latencia_promedio_ms} ms`;
                document.getElementById('badgeAlgoritmoHero').textContent = `Algoritmo: ${r.algoritmo_nombre}`;
                document.getElementById('descAlgoritmo').textContent = r.algoritmo_descripcion;
                document.getElementById('selectAlgoritmo').value = d.algoritmo;

                const heroBadge = document.getElementById('badgeClusterEstado');
                heroBadge.innerHTML = `<i class="bi bi-circle-fill me-1" style="font-size: 0.6rem;"></i> CLÚSTER ${r.estado_cluster}`;
                if (r.nodos_online === r.nodos_totales) {
                    heroBadge.style.background = 'rgba(16, 185, 129, 0.2)';
                } else if (r.nodos_online > 0) {
                    heroBadge.style.background = 'rgba(245, 158, 11, 0.28)';
                } else {
                    heroBadge.style.background = 'rgba(239, 68, 68, 0.32)';
                }

                // Render nodes
                const container = document.getElementById('nodosContainer');
                container.innerHTML = d.nodos.map(nodo => {
                    const online = nodo.estado === 'ONLINE';
                    return `
                        <div class="col-md-4">
                            <div class="p-4 rounded-3 border h-100 ${online ? 'bg-white' : 'bg-light opacity-75'}" style="border-width: 1.5px !important;">
                                <div class="d-flex align-items-center justify-content-between mb-2">
                                    <div>
                                        <span class="fw-bold text-dark fs-6">${nodo.id}</span>
                                        <span class="badge bg-light text-muted border ms-1 fs-8">Peso ${nodo.peso}</span>
                                    </div>
                                    <span class="badge ${online ? 'bg-success' : 'bg-danger'} px-2 py-1 fs-8">${nodo.estado}</span>
                                </div>
                                <div class="text-muted fs-8 mb-3">${nodo.nombre} · <code>${nodo.host}:${nodo.puerto}</code></div>
                                <div class="d-flex justify-content-between fs-8 mb-1">
                                    <span class="text-muted">Distribución de carga</span>
                                    <strong class="text-dark">${nodo.peticiones} req (${nodo.porcentaje}%)</strong>
                                </div>
                                <div class="progress mb-3" style="height: 8px;">
                                    <div class="progress-bar ${online ? 'bg-primary' : 'bg-secondary'}" role="progressbar" style="width: ${nodo.porcentaje}%;"></div>
                                </div>
                                <div class="row g-2 text-center mb-3 fs-8">
                                    <div class="col-6">
                                        <div class="p-2 rounded bg-light border">
                                            <div class="text-muted">Conexiones</div>
                                            <div class="fw-bold text-dark fs-6">${nodo.conexiones_activas}</div>
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="p-2 rounded bg-light border">
                                            <div class="text-muted">Latencia</div>
                                            <div class="fw-bold text-dark fs-6">${online ? nodo.latencia_ms + ' ms' : '—'}</div>
                                        </div>
                                    </div>
                                </div>
                                <div class="text-muted fs-8 mb-3 text-truncate" title="${nodo.ultimo_evento}">
                                    <i class="bi bi-clock me-1"></i>${nodo.ultimo_evento}
                                </div>
                                <button type="button"
                                        class="btn btn-sm w-100 fw-semibold btn-toggle-nodo ${online ? 'btn-outline-danger' : 'btn-success'}"
                                        data-nodo-id="${nodo.id}">
                                    <i class="bi ${online ? 'bi-power' : 'bi-check-circle-fill'} me-1"></i>
                                    ${online ? 'Simular Caída (OFFLINE)' : 'Restaurar Nodo (ONLINE)'}
                                </button>
                            </div>
                        </div>
                    `;
                }).join('');

                // Render history
                const tbody = document.getElementById('historialBody');
                if (!d.historial || d.historial.length === 0) {
                    tbody.innerHTML = `<tr><td colspan="5" class="text-center text-muted py-4">Haz clic en <strong>Despachar 1 Petición</strong> o <strong>Ráfaga (10)</strong> para observar el balanceo en tiempo real.</td></tr>`;
                } else {
                    tbody.innerHTML = d.historial.map(reg => `
                        <tr>
                            <td class="fw-bold"><code>${reg.id}</code></td>
                            <td class="text-muted">${reg.hora}</td>
                            <td><code>${reg.ruta}</code></td>
                            <td><span class="badge bg-light-primary text-primary">${reg.nodo_id}</span></td>
                            <td class="text-end fw-semibold text-success">${reg.latencia_ms} ms</td>
                        </tr>
                    `).join('');
                }
            }

            async function postAccion(url, body = {}) {
                const res = await fetch(url, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify(body)
                });
                const payload = await res.json();
                if (payload.datos) {
                    renderEstado(payload.datos);
                }
                return { ok: res.ok, payload };
            }

            // Despachar 1 petición
            document.getElementById('btnDespacharUno')?.addEventListener('click', async () => {
                const ruta = document.getElementById('selectRutaPrueba').value;
                const { ok, payload } = await postAccion("{{ route('balanceo.despachar') }}", { cantidad: 1, ruta });
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: ok ? 'success' : 'error',
                    title: payload.mensaje,
                    showConfirmButton: false,
                    timer: 2200
                });
            });

            // Ráfaga de 10 peticiones
            document.getElementById('btnDespacharRafaga')?.addEventListener('click', async () => {
                const ruta = document.getElementById('selectRutaPrueba').value;
                const { ok, payload } = await postAccion("{{ route('balanceo.despachar') }}", { cantidad: 10, ruta });
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: ok ? 'success' : 'error',
                    title: payload.mensaje,
                    showConfirmButton: false,
                    timer: 2400
                });
            });

            // Cambiar algoritmo
            document.getElementById('selectAlgoritmo')?.addEventListener('change', async (e) => {
                const { ok, payload } = await postAccion("{{ route('balanceo.algoritmo') }}", { algoritmo: e.target.value });
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: ok ? 'info' : 'error',
                    title: payload.mensaje,
                    showConfirmButton: false,
                    timer: 2200
                });
            });

            // Alternar nodo (delegación de eventos)
            document.getElementById('nodosContainer')?.addEventListener('click', async (e) => {
                const btn = e.target.closest('.btn-toggle-nodo');
                if (!btn) return;
                const nodoId = btn.getAttribute('data-nodo-id');
                const { ok, payload } = await postAccion(`${toggleBaseUrl}/${nodoId}/toggle`);
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: ok ? 'warning' : 'error',
                    title: payload.mensaje,
                    showConfirmButton: false,
                    timer: 2200
                });
            });

            // Reiniciar clúster
            document.getElementById('btnReiniciarCluster')?.addEventListener('click', async () => {
                const { ok, payload } = await postAccion("{{ route('balanceo.reiniciar') }}");
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: ok ? 'success' : 'error',
                    title: payload.mensaje,
                    showConfirmButton: false,
                    timer: 2200
                });
            });
        });
    </script>
@endsection
