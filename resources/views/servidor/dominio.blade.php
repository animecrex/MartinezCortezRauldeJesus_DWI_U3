@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(16, 185, 129, 0.2); color: #d1fae5; border: 1px solid rgba(16, 185, 129, 0.4);">
                        <i class="bi bi-globe2 me-1"></i>
                        <span id="badgeDominioHero">{{ $dominio['config']['dominio_principal'] }}</span>
                    </span>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.14); color: #e2e8f0;" id="badgeHttpsHero">
                        <i class="bi bi-lock-fill me-1"></i>
                        {{ $dominio['config']['forzar_https'] ? 'HTTPS / SSL Activo' : 'HTTP Local' }}
                    </span>
                </div>
                <h1 class="page-hero-title">Configuración de Dominio 🌐</h1>
                <p class="page-hero-subtitle mb-0">
                    Administración de dominio principal, zona DNS, verificación de resolución en vivo, política HTTPS/SSL y generación automática de VirtualHost Apache y variables Laravel.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                <button type="button" id="btnRestablecerDominio" class="btn-modern-outline" style="background: rgba(255,255,255,0.12); color: #fff; border-color: rgba(255,255,255,0.3);">
                    <i class="bi bi-arrow-counterclockwise"></i>
                    <span>Restablecer</span>
                </button>
            </div>
        </div>

        <!-- KPI Summary Row -->
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Dominio Principal</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-globe fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1 text-truncate" id="kpiDominioPrincipal">
                        {{ $dominio['config']['dominio_principal'] }}
                    </div>
                    <div class="text-muted fs-8 text-truncate" id="kpiUrlPrincipal">
                        {{ $dominio['urls']['principal'] }}
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Host Activo (Petición)</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669;">
                            <i class="bi bi-pc-display-horizontal fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1">
                        {{ $dominio['entorno_actual']['host_peticion'] }}:{{ $dominio['entorno_actual']['puerto_peticion'] }}
                    </div>
                    <div class="text-muted fs-8">
                        Esquema actual: <strong>{{ $dominio['entorno_actual']['esquema_peticion'] }}</strong>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Política SSL / TLS</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-shield-lock-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1" id="kpiSslEstado">
                        {{ $dominio['config']['forzar_https'] ? 'HTTPS Forzado' : 'HTTP Estándar' }}
                    </div>
                    <div class="text-muted fs-8">
                        IP Destino: <code id="kpiIpDestino">{{ $dominio['config']['ip_destino'] }}</code>
                    </div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Zona DNS & API</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                            <i class="bi bi-signpost-split-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1">
                        {{ count($dominio['registros_dns']) }} registros
                    </div>
                    <div class="text-muted fs-8 text-truncate" id="kpiUrlApi">
                        {{ $dominio['urls']['api'] }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Row: Domain Config Form + Live DNS Verifier -->
        <div class="row g-5 mb-6">
            <!-- Form -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-gear-fill text-primary fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Parámetros del Dominio</h3>
                                <span class="text-muted fs-8">Personaliza el dominio y regenera automáticamente DNS, Apache VirtualHost y <code>.env</code>.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <form id="formConfigDominio">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold fs-8" for="dominio_principal">Dominio Principal <span class="text-danger">*</span></label>
                                    <input type="text" id="dominio_principal" name="dominio_principal"
                                           value="{{ $dominio['config']['dominio_principal'] }}"
                                           class="form-control form-control-sm" placeholder="ej. cursoplus.local" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold fs-8" for="subdominio_api">Subdominio API <span class="text-danger">*</span></label>
                                    <input type="text" id="subdominio_api" name="subdominio_api"
                                           value="{{ $dominio['config']['subdominio_api'] }}"
                                           class="form-control form-control-sm" placeholder="api" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold fs-8" for="ip_destino">IP de Destino (Registro A) <span class="text-danger">*</span></label>
                                    <input type="text" id="ip_destino" name="ip_destino"
                                           value="{{ $dominio['config']['ip_destino'] }}"
                                           class="form-control form-control-sm" placeholder="127.0.0.1" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold fs-8" for="ttl_dns">TTL DNS (segundos)</label>
                                    <input type="number" id="ttl_dns" name="ttl_dns" min="60" max="86400"
                                           value="{{ $dominio['config']['ttl_dns'] }}"
                                           class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="form-check form-switch mb-4">
                                <input class="form-check-input" type="checkbox" role="switch" id="forzar_https" name="forzar_https"
                                       {{ $dominio['config']['forzar_https'] ? 'checked' : '' }}>
                                <label class="form-check-label fs-8 fw-semibold text-dark" for="forzar_https">
                                    Forzar HTTPS / SSL (Redirección 301, HSTS y Cookies <code>Secure</code>)
                                </label>
                            </div>

                            <button type="submit" id="btnGuardarDominio" class="btn-modern-primary w-100 justify-content-center">
                                <i class="bi bi-save-fill"></i>
                                <span>Guardar y Regenerar Plantillas</span>
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Live DNS Verifier + Active Laravel Context -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-search text-info fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Verificador de Resolución DNS en Vivo</h3>
                                <span class="text-muted fs-8">Comprueba resolución de host local o externo desde el servidor PHP.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="input-group input-group-sm mb-3">
                            <input type="text" id="inputDnsLookup" class="form-control"
                                   value="{{ $dominio['entorno_actual']['host_peticion'] }}"
                                   placeholder="Ej. localhost, open.er-api.com, cursoplus.local">
                            <button type="button" id="btnVerificarDns" class="btn btn-primary fw-semibold">
                                <i class="bi bi-broadcast me-1"></i> Verificar DNS
                            </button>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-light border fs-8 btn-quick-dns" data-host="localhost">localhost</button>
                            <button type="button" class="btn btn-sm btn-light border fs-8 btn-quick-dns" data-host="open.er-api.com">open.er-api.com</button>
                            <button type="button" class="btn btn-sm btn-light border fs-8 btn-quick-dns" id="btnQuickConfiguredDns" data-host="{{ $dominio['config']['dominio_principal'] }}">{{ $dominio['config']['dominio_principal'] }}</button>
                        </div>

                        <div id="dnsResultBox" class="p-3 rounded-3 border bg-light fs-8 mb-4">
                            <div class="text-muted">Haz clic en <strong>Verificar DNS</strong> para consultar la resolución IP y latencia en tiempo real.</div>
                        </div>

                        <div class="fw-bold text-dark fs-8 mb-2">Contexto Activo de Dominio en Laravel</div>
                        <div class="table-responsive">
                            <table class="table table-sm align-middle mb-0 fs-8">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">APP_URL actual</td>
                                        <td class="text-end"><code>{{ $dominio['entorno_actual']['app_url'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">SESSION_DOMAIN</td>
                                        <td class="text-end"><code>{{ $dominio['entorno_actual']['session_domain'] }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Cookies HTTPS / SameSite</td>
                                        <td class="text-end">{{ $dominio['entorno_actual']['session_secure'] }} · <code>{{ $dominio['entorno_actual']['session_same_site'] }}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- DNS Zone Table -->
        <div class="modern-card mb-6">
            <div class="modern-card-header">
                <div class="d-flex align-items-center gap-2">
                    <i class="bi bi-table text-success fs-4"></i>
                    <div>
                        <h3 class="modern-card-title mb-0">Tabla de Zona DNS Configurada</h3>
                        <span class="text-muted fs-8">Registros recomendados para apuntar el dominio hacia el servidor de aplicaciones Curso+.</span>
                    </div>
                </div>
            </div>
            <div class="p-4">
                <div class="table-responsive">
                    <table class="table table-hover table-sm align-middle mb-0 fs-8">
                        <thead>
                            <tr class="text-muted border-bottom">
                                <th>Tipo</th>
                                <th>Host / Nombre</th>
                                <th>Valor / Destino</th>
                                <th class="text-end">TTL</th>
                                <th>Propósito</th>
                            </tr>
                        </thead>
                        <tbody id="tablaDnsBody">
                            @foreach($dominio['registros_dns'] as $reg)
                                <tr>
                                    <td><span class="badge bg-light-primary text-primary fw-bold">{{ $reg['tipo'] }}</span></td>
                                    <td><code>{{ $reg['host'] }}</code></td>
                                    <td><code>{{ $reg['destino'] }}</code></td>
                                    <td class="text-end">{{ $reg['ttl'] }} s</td>
                                    <td class="text-muted">{{ $reg['proposito'] }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Deployment Templates: Apache VirtualHost, Windows Hosts, Laravel .env -->
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-file-earmark-code-fill text-warning fs-4"></i>
                                <div>
                                    <h3 class="modern-card-title mb-0">Apache VirtualHost</h3>
                                    <span class="text-muted fs-8"><code>C:\xampp\apache\conf\extra\httpd-vhosts.conf</code></span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border btn-copy" data-target="codeApacheVhost">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <pre id="codeApacheVhost" class="p-3 rounded-3 mb-0" style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.76rem; max-height: 290px; overflow: auto;">{{ $dominio['plantillas']['apache_vhost'] }}</pre>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-terminal-fill text-info fs-4"></i>
                                <div>
                                    <h3 class="modern-card-title mb-0">Archivo <code>hosts</code> y Variables <code>.env</code></h3>
                                    <span class="text-muted fs-8">Mapeo local en Windows y configuración de sesión/Sanctum en Laravel.</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold fs-8 text-dark">Archivo <code>hosts</code> (Windows)</span>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 fs-8 btn-copy" data-target="codeHostsWindows">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                        <pre id="codeHostsWindows" class="p-3 rounded-3 mb-3" style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.76rem; overflow: auto;">{{ $dominio['plantillas']['hosts_windows'] }}</pre>

                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold fs-8 text-dark">Variables de Dominio (<code>.env</code>)</span>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 fs-8 btn-copy" data-target="codeEnvLaravel">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                        <pre id="codeEnvLaravel" class="p-3 rounded-3 mb-0" style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.76rem; overflow: auto;">{{ $dominio['plantillas']['env_laravel'] }}</pre>
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

            function aplicarDatos(d) {
                const c = d.config;
                document.getElementById('badgeDominioHero').textContent = c.dominio_principal;
                document.getElementById('badgeHttpsHero').innerHTML = `<i class="bi bi-lock-fill me-1"></i> ${c.forzar_https ? 'HTTPS / SSL Activo' : 'HTTP Local'}`;
                document.getElementById('kpiDominioPrincipal').textContent = c.dominio_principal;
                document.getElementById('kpiUrlPrincipal').textContent = d.urls.principal;
                document.getElementById('kpiSslEstado').textContent = c.forzar_https ? 'HTTPS Forzado' : 'HTTP Estándar';
                document.getElementById('kpiIpDestino').textContent = c.ip_destino;
                document.getElementById('kpiUrlApi').textContent = d.urls.api;

                document.getElementById('dominio_principal').value = c.dominio_principal;
                document.getElementById('subdominio_api').value = c.subdominio_api;
                document.getElementById('ip_destino').value = c.ip_destino;
                document.getElementById('ttl_dns').value = c.ttl_dns;
                document.getElementById('forzar_https').checked = !!c.forzar_https;

                const quickBtn = document.getElementById('btnQuickConfiguredDns');
                if (quickBtn) {
                    quickBtn.setAttribute('data-host', c.dominio_principal);
                    quickBtn.textContent = c.dominio_principal;
                }

                document.getElementById('tablaDnsBody').innerHTML = d.registros_dns.map(reg => `
                    <tr>
                        <td><span class="badge bg-light-primary text-primary fw-bold">${reg.tipo}</span></td>
                        <td><code>${reg.host}</code></td>
                        <td><code>${reg.destino}</code></td>
                        <td class="text-end">${reg.ttl} s</td>
                        <td class="text-muted">${reg.proposito}</td>
                    </tr>
                `).join('');

                document.getElementById('codeApacheVhost').textContent = d.plantillas.apache_vhost;
                document.getElementById('codeHostsWindows').textContent = d.plantillas.hosts_windows;
                document.getElementById('codeEnvLaravel').textContent = d.plantillas.env_laravel;
            }

            // 1. Guardar configuración de dominio
            document.getElementById('formConfigDominio')?.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('btnGuardarDominio');
                btn.disabled = true;

                try {
                    const res = await fetch("{{ route('dominio.actualizar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            dominio_principal: document.getElementById('dominio_principal').value,
                            subdominio_api: document.getElementById('subdominio_api').value,
                            ip_destino: document.getElementById('ip_destino').value,
                            ttl_dns: parseInt(document.getElementById('ttl_dns').value, 10),
                            forzar_https: document.getElementById('forzar_https').checked
                        })
                    });
                    const payload = await res.json();
                    if (res.ok && payload.exito) {
                        aplicarDatos(payload.datos);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: payload.mensaje,
                            showConfirmButton: false,
                            timer: 2400
                        });
                    } else {
                        const firstErr = payload.errors ? Object.values(payload.errors)[0][0] : (payload.mensaje || 'Error de validación');
                        Swal.fire('Validación', firstErr, 'warning');
                    }
                } catch (err) {
                    Swal.fire('Error', 'No se pudo actualizar la configuración de dominio.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });

            // 2. Verificador DNS en vivo
            async function ejecutarLookup(host) {
                const box = document.getElementById('dnsResultBox');
                box.innerHTML = `<span class="text-muted">Consultando resolución DNS para <code>${host}</code>...</span>`;
                try {
                    const res = await fetch("{{ route('dominio.verificar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ dominio: host })
                    });
                    const payload = await res.json();
                    if (res.ok && payload.datos) {
                        const r = payload.datos;
                        const badge = r.resuelto
                            ? '<span class="badge bg-success">RESUELTO</span>'
                            : '<span class="badge bg-warning text-dark">NO RESUELTO</span>';
                        box.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between mb-1">
                                <strong><code>${r.dominio}</code></strong>
                                ${badge}
                            </div>
                            <div>IP resuelta: <code>${r.ip_resuelta}</code> · Latencia: <strong>${r.latencia_ms} ms</strong> (${r.hora})</div>
                        `;
                    } else {
                        box.innerHTML = `<span class="text-danger">${payload.message || 'Dominio no válido.'}</span>`;
                    }
                } catch (err) {
                    box.innerHTML = `<span class="text-danger">Error de comunicación al verificar DNS.</span>`;
                }
            }

            document.getElementById('btnVerificarDns')?.addEventListener('click', () => {
                const host = document.getElementById('inputDnsLookup').value.trim();
                if (host) ejecutarLookup(host);
            });

            document.querySelectorAll('.btn-quick-dns').forEach(btn => {
                btn.addEventListener('click', () => {
                    const host = btn.getAttribute('data-host');
                    document.getElementById('inputDnsLookup').value = host;
                    ejecutarLookup(host);
                });
            });

            // 3. Restablecer configuración
            document.getElementById('btnRestablecerDominio')?.addEventListener('click', async () => {
                const res = await fetch("{{ route('dominio.restablecer') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const payload = await res.json();
                if (res.ok && payload.datos) {
                    aplicarDatos(payload.datos);
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'info',
                        title: payload.mensaje,
                        showConfirmButton: false,
                        timer: 2200
                    });
                }
            });

            // 4. Botones de copiar plantillas
            document.querySelectorAll('.btn-copy').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const target = document.getElementById(btn.getAttribute('data-target'));
                    if (!target) return;
                    try {
                        await navigator.clipboard.writeText(target.textContent);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Configuración copiada al portapapeles',
                            showConfirmButton: false,
                            timer: 1800
                        });
                    } catch (e) {
                        // Fallback silencioso si el navegador bloquea clipboard en HTTP
                    }
                });
            });
        });
    </script>
@endsection
