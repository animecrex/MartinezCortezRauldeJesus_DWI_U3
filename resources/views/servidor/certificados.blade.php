@extends('layouts.app')

@section('content')
    @php $cert = $ssl['certificado_activo']; @endphp
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <div class="d-flex flex-wrap align-items-center gap-2 mb-2">
                    <span id="badgeCertEstado"
                          class="badge rounded-pill px-3 py-2"
                          style="background: {{ ($cert['estado'] ?? '') === 'VIGENTE' ? 'rgba(16, 185, 129, 0.22)' : 'rgba(245, 158, 11, 0.28)' }}; color: #fff; border: 1px solid rgba(255,255,255,0.35);">
                        <i class="bi bi-patch-check-fill me-1"></i>
                        CERTIFICADO {{ $cert['estado'] ?? 'NO CONFIGURADO' }}
                    </span>
                    <span class="badge rounded-pill px-3 py-2" style="background: rgba(255, 255, 255, 0.14); color: #e2e8f0;">
                        {{ $ssl['motor']['openssl_version'] }}
                    </span>
                </div>
                <h1 class="page-hero-title">Certificados de Seguridad (SSL / TLS) 🔒</h1>
                <p class="page-hero-subtitle mb-0">
                    Gestión de certificados digitales X.509, generación RSA-2048 con OpenSSL, inspección TLS en vivo y configuración HTTPS/HSTS para Curso+.
                </p>
            </div>
            <div class="d-flex flex-wrap gap-2 mt-3 mt-md-0">
                @if($ssl['tiene_certificado_descargable'])
                    <a href="{{ route('certificados.descargar') }}" id="btnDescargarCert" class="btn-modern-primary text-decoration-none">
                        <i class="bi bi-download"></i>
                        <span>Descargar Certificado (.crt)</span>
                    </a>
                @endif
            </div>
        </div>

        <!-- KPI Row -->
        <div class="row g-4 mb-6">
            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Titular (Common Name)</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #ecfdf5; color: #059669;">
                            <i class="bi bi-shield-fill-check fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1 text-truncate" id="kpiCertCn">{{ $cert['cn'] ?? 'Sin certificado' }}</div>
                    <div class="text-muted fs-8 text-truncate" id="kpiCertEmisor">Emisor: {{ $cert['emisor_cn'] ?? '—' }}</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Vigencia X.509</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #eff6ff; color: #2563eb;">
                            <i class="bi bi-calendar2-check-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1" id="kpiDiasRestantes">
                        {{ isset($cert['dias_restantes']) ? $cert['dias_restantes'] . ' días' : 'N/D' }}
                    </div>
                    <div class="text-muted fs-8" id="kpiValidoHasta">Vence: {{ $cert['valido_hasta'] ?? '—' }}</div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Algoritmo & Cifrado</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #fef3c7; color: #d97706;">
                            <i class="bi bi-key-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-5 text-dark mb-1 text-truncate" id="kpiAlgoritmo">{{ $cert['algoritmo_firma'] ?? 'RSA-SHA256' }}</div>
                    <div class="text-muted fs-8">App Cipher: <code>{{ $ssl['motor']['laravel_cipher'] }}</code></div>
                </div>
            </div>

            <div class="col-sm-6 col-xl-3">
                <div class="modern-card p-4 h-100">
                    <div class="d-flex align-items-center justify-content-between mb-2">
                        <span class="text-muted fw-semibold fs-7">Transporte Activo</span>
                        <div class="d-flex align-items-center justify-content-center rounded-circle" style="width: 40px; height: 40px; background: #f0f9ff; color: #0284c7;">
                            <i class="bi bi-lock-fill fs-5"></i>
                        </div>
                    </div>
                    <div class="fw-bold fs-4 text-dark mb-1">
                        {{ $ssl['motor']['esquema_actual'] }} :{{ $ssl['motor']['puerto_actual'] }}
                    </div>
                    <div class="text-muted fs-8">
                        {{ $ssl['motor']['peticion_https'] ? 'Conexión cifrada TLS activa' : 'Modo local HTTP (SSL listo en :443)' }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Middle Row: Active Certificate Details + Real X.509 Generator -->
        <div class="row g-5 mb-6">
            <!-- Active Certificate Inspector -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-file-earmark-lock2-fill text-success fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Certificado Digital X.509 Activo</h3>
                                <span class="text-muted fs-8" id="labelOrigenCert">{{ $ssl['origen_certificado'] }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="table-responsive mb-3">
                            <table class="table table-sm align-middle mb-0 fs-8">
                                <tbody>
                                    <tr>
                                        <td class="text-muted fw-semibold">Common Name (CN)</td>
                                        <td class="text-end fw-bold text-dark"><code id="detCn">{{ $cert['cn'] ?? '—' }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Organización / Unidad</td>
                                        <td class="text-end text-dark" id="detOrg">{{ ($cert['organizacion'] ?? '—') . ' · ' . ($cert['unidad'] ?? '—') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Emisor (Issuer)</td>
                                        <td class="text-end text-dark" id="detEmisor">{{ $cert['emisor_cn'] ?? '—' }} ({{ $cert['emisor_o'] ?? '—' }})</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Versión / Algoritmo</td>
                                        <td class="text-end text-dark" id="detAlg">{{ ($cert['version_x509'] ?? 'v3') . ' · ' . ($cert['algoritmo_firma'] ?? '—') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Número de Serie</td>
                                        <td class="text-end"><code id="detSerie">{{ $cert['serie'] ?? '—' }}</code></td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Validez</td>
                                        <td class="text-end text-dark" id="detValidez">{{ ($cert['valido_desde'] ?? '—') . ' → ' . ($cert['valido_hasta'] ?? '—') }}</td>
                                    </tr>
                                    <tr>
                                        <td class="text-muted fw-semibold">Huella SHA-256</td>
                                        <td class="text-end"><code class="d-inline-block text-break" style="max-width: 260px; font-size: 0.72rem;" id="detHuella">{{ $cert['huella_sha256'] ?? '—' }}</code></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-1">
                            <span class="fw-bold fs-8 text-dark">Certificado Público (Formato PEM)</span>
                            <button type="button" class="btn btn-sm btn-light border py-0 px-2 fs-8 btn-copy" data-target="codeCertPem">
                                <i class="bi bi-clipboard"></i> Copiar PEM
                            </button>
                        </div>
                        <pre id="codeCertPem" class="p-3 rounded-3 mb-0" style="background: #0f172a; color: #86efac; font-family: Consolas, monospace; font-size: 0.72rem; max-height: 155px; overflow: auto;">{{ $ssl['pem_publico'] ?? 'Sin certificado PEM cargado.' }}</pre>
                    </div>
                </div>
            </div>

            <!-- Real OpenSSL Self-Signed Certificate Generator -->
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-award-fill text-primary fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Generar Certificado X.509 Autofirmado (OpenSSL)</h3>
                                <span class="text-muted fs-8">Emite un certificado real RSA 2048-bit + SHA-256 y resguarda la llave privada en <code>storage/app/ssl/</code>.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <form id="formGenerarCert">
                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold fs-8" for="cn">Common Name / Dominio (CN) <span class="text-danger">*</span></label>
                                    <input type="text" id="cn" name="cn" value="cursoplus.local" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold fs-8" for="dias">Vigencia (días) <span class="text-danger">*</span></label>
                                    <input type="number" id="dias" name="dias" value="365" min="30" max="1095" class="form-control form-control-sm" required>
                                </div>
                            </div>

                            <div class="row g-3 mb-3">
                                <div class="col-md-7">
                                    <label class="form-label fw-semibold fs-8" for="organizacion">Organización (O) <span class="text-danger">*</span></label>
                                    <input type="text" id="organizacion" name="organizacion" value="Curso+ Plataforma Educativa" class="form-control form-control-sm" required>
                                </div>
                                <div class="col-md-5">
                                    <label class="form-label fw-semibold fs-8" for="pais">País (ISO-2) <span class="text-danger">*</span></label>
                                    <input type="text" id="pais" name="pais" value="MX" maxlength="2" class="form-control form-control-sm text-uppercase" required>
                                </div>
                            </div>

                            <div class="mb-4">
                                <label class="form-label fw-semibold fs-8" for="unidad">Unidad Organizacional (OU)</label>
                                <input type="text" id="unidad" name="unidad" value="Ingenieria DWI" class="form-control form-control-sm">
                            </div>

                            <button type="submit" id="btnGenerarCert" class="btn-modern-primary w-100 justify-content-center mb-4">
                                <i class="bi bi-shield-plus"></i>
                                <span>Generar Certificado SSL RSA-2048</span>
                            </button>
                        </form>

                        <div class="fw-bold text-dark fs-8 mb-2">Controles Criptográficos y TLS Aplicados</div>
                        <ul class="list-unstyled mb-0">
                            @foreach($ssl['protecciones_tls'] as $prot)
                                <li class="d-flex align-items-start gap-2 mb-1 fs-8 text-dark">
                                    <i class="bi bi-check-circle-fill text-success mt-1"></i>
                                    <span>{{ $prot }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Live TLS Endpoint Inspector + Apache mod_ssl / Certbot -->
        <div class="row g-5">
            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center gap-2">
                            <i class="bi bi-radar text-info fs-4"></i>
                            <div>
                                <h3 class="modern-card-title mb-0">Inspector SSL/TLS en Tiempo Real</h3>
                                <span class="text-muted fs-8">Realiza un handshake TLS real para inspeccionar el certificado de un servidor local o externo.</span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <div class="row g-2 mb-3">
                            <div class="col-7">
                                <input type="text" id="inputTlsHost" class="form-control form-control-sm" value="open.er-api.com" placeholder="Ej. open.er-api.com o localhost">
                            </div>
                            <div class="col-2">
                                <input type="number" id="inputTlsPort" class="form-control form-control-sm" value="443" min="1" max="65535">
                            </div>
                            <div class="col-3">
                                <button type="button" id="btnInspeccionarTls" class="btn btn-sm btn-primary w-100 fw-semibold">
                                    <i class="bi bi-search"></i> Probar
                                </button>
                            </div>
                        </div>

                        <div class="d-flex flex-wrap gap-2 mb-3">
                            <button type="button" class="btn btn-sm btn-light border fs-8 btn-quick-tls" data-host="localhost" data-port="443">localhost:443 (XAMPP SSL)</button>
                            <button type="button" class="btn btn-sm btn-light border fs-8 btn-quick-tls" data-host="open.er-api.com" data-port="443">open.er-api.com:443 (WS Terceros)</button>
                        </div>

                        <div id="tlsInspectorBox" class="p-3 rounded-3 border bg-light fs-8">
                            <div class="text-muted">Selecciona un host y haz clic en <strong>Probar</strong> para inspeccionar el certificado X.509 negociado vía TLS.</div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-lg-6">
                <div class="modern-card h-100">
                    <div class="modern-card-header">
                        <div class="d-flex align-items-center justify-content-between w-100">
                            <div class="d-flex align-items-center gap-2">
                                <i class="bi bi-terminal-fill text-warning fs-4"></i>
                                <div>
                                    <h3 class="modern-card-title mb-0">Despliegue HTTPS: Apache <code>mod_ssl</code> & Let's Encrypt</h3>
                                    <span class="text-muted fs-8">Configuración endurecida TLS 1.2/1.3 + HSTS.</span>
                                </div>
                            </div>
                            <button type="button" class="btn btn-sm btn-light border btn-copy" data-target="codeApacheSsl">
                                <i class="bi bi-clipboard"></i> Copiar
                            </button>
                        </div>
                    </div>
                    <div class="p-4">
                        <pre id="codeApacheSsl" class="p-3 rounded-3 mb-3" style="background: #0f172a; color: #e2e8f0; font-family: Consolas, monospace; font-size: 0.75rem; overflow: auto;">{{ $ssl['config_apache_ssl'] }}</pre>
                        <div class="fw-bold fs-8 text-dark mb-1">Certificado Gratuito en Producción (Certbot / Let's Encrypt):</div>
                        <pre class="p-2 px-3 rounded-3 mb-0" style="background: #0f172a; color: #93c5fd; font-family: Consolas, monospace; font-size: 0.75rem; overflow: auto;">{{ $ssl['comando_certbot'] }}</pre>
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

            function actualizarVistaCert(ssl) {
                const c = ssl.certificado_activo;
                if (!c) return;
                document.getElementById('badgeCertEstado').innerHTML = `<i class="bi bi-patch-check-fill me-1"></i> CERTIFICADO ${c.estado}`;
                document.getElementById('kpiCertCn').textContent = c.cn;
                document.getElementById('kpiCertEmisor').textContent = 'Emisor: ' + c.emisor_cn;
                document.getElementById('kpiDiasRestantes').textContent = c.dias_restantes + ' días';
                document.getElementById('kpiValidoHasta').textContent = 'Vence: ' + c.valido_hasta;
                document.getElementById('kpiAlgoritmo').textContent = c.algoritmo_firma;
                document.getElementById('labelOrigenCert').textContent = ssl.origen_certificado;

                document.getElementById('detCn').textContent = c.cn;
                document.getElementById('detOrg').textContent = `${c.organizacion} · ${c.unidad}`;
                document.getElementById('detEmisor').textContent = `${c.emisor_cn} (${c.emisor_o})`;
                document.getElementById('detAlg').textContent = `${c.version_x509} · ${c.algoritmo_firma}`;
                document.getElementById('detSerie').textContent = c.serie;
                document.getElementById('detValidez').textContent = `${c.valido_desde} → ${c.valido_hasta}`;
                document.getElementById('detHuella').textContent = c.huella_sha256;
                document.getElementById('codeCertPem').textContent = ssl.pem_publico || '';
            }

            // 1. Generar certificado X.509 real
            document.getElementById('formGenerarCert')?.addEventListener('submit', async function (e) {
                e.preventDefault();
                const btn = document.getElementById('btnGenerarCert');
                btn.disabled = true;

                try {
                    const res = await fetch("{{ route('certificados.generar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({
                            cn: document.getElementById('cn').value,
                            organizacion: document.getElementById('organizacion').value,
                            unidad: document.getElementById('unidad').value,
                            pais: document.getElementById('pais').value.toUpperCase(),
                            dias: parseInt(document.getElementById('dias').value, 10)
                        })
                    });
                    const payload = await res.json();
                    if (res.ok && payload.exito) {
                        actualizarVistaCert(payload.datos);
                        Swal.fire({
                            icon: 'success',
                            title: 'Certificado X.509 Generado',
                            text: payload.mensaje,
                            timer: 2800,
                            showConfirmButton: false
                        });
                    } else {
                        const firstErr = payload.errors ? Object.values(payload.errors)[0][0] : (payload.mensaje || 'Error al generar certificado');
                        Swal.fire('Atención', firstErr, 'warning');
                    }
                } catch (err) {
                    Swal.fire('Error', 'No se pudo generar el certificado SSL.', 'error');
                } finally {
                    btn.disabled = false;
                }
            });

            // 2. Inspector TLS en vivo
            async function inspeccionarTls(host, puerto) {
                const box = document.getElementById('tlsInspectorBox');
                box.innerHTML = `<span class="text-muted">Iniciando handshake TLS con <code>${host}:${puerto}</code>...</span>`;
                try {
                    const res = await fetch("{{ route('certificados.inspeccionar') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Accept': 'application/json',
                            'X-CSRF-TOKEN': csrfToken
                        },
                        body: JSON.stringify({ host, puerto: parseInt(puerto, 10) })
                    });
                    const payload = await res.json();
                    if (res.ok && payload.exito && payload.datos?.certificado) {
                        const d = payload.datos;
                        const c = d.certificado;
                        box.innerHTML = `
                            <div class="d-flex align-items-center justify-content-between mb-2">
                                <strong><code>${d.host}:${d.puerto}</code></strong>
                                <span class="badge bg-success">${c.estado} · ${d.latencia_ms} ms</span>
                            </div>
                            <div class="mb-1"><strong>CN:</strong> <code>${c.cn}</code> · <strong>Emisor:</strong> ${c.emisor_cn} (${c.emisor_o})</div>
                            <div class="mb-1"><strong>Validez:</strong> ${c.valido_desde} → ${c.valido_hasta} (<strong>${c.dias_restantes} días</strong>)</div>
                            <div class="mb-1"><strong>Protocolo:</strong> <code>${d.protocolo_negociado}</code> · <strong>Firma:</strong> <code>${c.algoritmo_firma}</code></div>
                            <div class="text-break"><strong>SHA-256:</strong> <code style="font-size:0.72rem;">${c.huella_sha256}</code></div>
                        `;
                    } else {
                        box.innerHTML = `<div class="text-danger"><i class="bi bi-exclamation-triangle-fill me-1"></i>${payload.mensaje || 'No se pudo completar la inspección TLS.'}</div>`;
                    }
                } catch (err) {
                    box.innerHTML = `<div class="text-danger">Error de red al inspeccionar el certificado TLS.</div>`;
                }
            }

            document.getElementById('btnInspeccionarTls')?.addEventListener('click', () => {
                const host = document.getElementById('inputTlsHost').value.trim();
                const port = document.getElementById('inputTlsPort').value.trim() || 443;
                if (host) inspeccionarTls(host, port);
            });

            document.querySelectorAll('.btn-quick-tls').forEach(btn => {
                btn.addEventListener('click', () => {
                    const host = btn.getAttribute('data-host');
                    const port = btn.getAttribute('data-port');
                    document.getElementById('inputTlsHost').value = host;
                    document.getElementById('inputTlsPort').value = port;
                    inspeccionarTls(host, port);
                });
            });

            // 3. Copiar bloques
            document.querySelectorAll('.btn-copy').forEach(btn => {
                btn.addEventListener('click', async () => {
                    const el = document.getElementById(btn.getAttribute('data-target'));
                    if (!el) return;
                    try {
                        await navigator.clipboard.writeText(el.textContent);
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'success',
                            title: 'Copiado al portapapeles',
                            showConfirmButton: false,
                            timer: 1800
                        });
                    } catch (e) {}
                });
            });
        });
    </script>
@endsection
