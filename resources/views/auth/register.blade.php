<!DOCTYPE html>
<html lang="es">

<head>
    <title>Crear Cuenta | Curso+</title>
    <meta charset="utf-8" />
    <meta name="description" content="Regístrate en Curso+ y accede a la mejor plataforma de cursos en línea." />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Core Theme Styles -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --brand-gradient-glow: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        }

        * {
            box-sizing: border-box;
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
            margin: 0;
        }

        .auth-container {
            min-height: 100vh;
        }

        /* Form Column Styling */
        .auth-form-area {
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 1.5rem;
            position: relative;
        }

        @media (min-width: 992px) {
            .auth-form-area {
                padding: 3rem 4rem;
            }
        }

        .auth-card-inner {
            width: 100%;
            max-width: 460px;
        }

        /* Logo Brand Style */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            margin-bottom: 1.75rem;
        }

        .brand-icon-box {
            width: 44px;
            height: 44px;
            border-radius: 12px;
            background: var(--brand-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            color: #ffffff;
            font-size: 1.35rem;
            box-shadow: 0 8px 16px -2px rgba(37, 99, 235, 0.35);
        }

        .brand-text {
            font-size: 1.45rem;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: -0.5px;
        }

        .brand-text span {
            color: var(--brand-primary);
        }

        /* Input Controls */
        .custom-input-wrapper {
            position: relative;
            margin-bottom: 1.15rem;
        }

        .custom-input-wrapper .input-icon-left {
            position: absolute;
            left: 1rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
            font-size: 1.15rem;
            transition: color 0.2s ease;
            pointer-events: none;
            z-index: 4;
        }

        .custom-input-wrapper .form-control {
            height: 50px;
            padding-left: 2.85rem;
            padding-right: 1rem;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background-color: #f8fafc;
            font-size: 0.95rem;
            font-weight: 500;
            color: #1e293b;
            transition: all 0.25s cubic-bezier(0.4, 0, 0.2, 1);
        }

        .custom-input-wrapper .form-control:focus {
            background-color: #ffffff;
            border-color: #3b82f6;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.15);
            outline: none;
        }

        .custom-input-wrapper .form-control:focus + .input-icon-left,
        .custom-input-wrapper:focus-within .input-icon-left {
            color: #2563eb;
        }

        .custom-input-wrapper.has-toggle .form-control {
            padding-right: 3rem;
        }

        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            padding: 0.35rem 0.5rem;
            color: #94a3b8;
            font-size: 1.2rem;
            cursor: pointer;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
            z-index: 5;
        }

        .password-toggle-btn:hover {
            color: #2563eb;
            background-color: rgba(37, 99, 235, 0.08);
        }

        /* Action Button */
        .btn-brand-primary {
            background: var(--brand-gradient);
            color: #ffffff;
            border: none;
            border-radius: 12px;
            height: 50px;
            font-weight: 600;
            font-size: 1rem;
            letter-spacing: 0.2px;
            box-shadow: 0 6px 18px -2px rgba(37, 99, 235, 0.4);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            transition: all 0.25s ease;
            width: 100%;
        }

        .btn-brand-primary:hover:not(:disabled) {
            background: var(--brand-gradient-glow);
            color: #ffffff;
            transform: translateY(-2px);
            box-shadow: 0 10px 24px -2px rgba(37, 99, 235, 0.5);
        }

        .btn-brand-primary:active:not(:disabled) {
            transform: translateY(0);
            box-shadow: 0 4px 12px -2px rgba(37, 99, 235, 0.3);
        }

        .btn-brand-primary:disabled {
            opacity: 0.75;
            cursor: not-allowed;
        }

        /* Right Hero Section */
        .hero-banner {
            position: relative;
            background-size: cover;
            background-position: center;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: url('{{ asset("assets/media/misc/curso.jpg") }}');
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.92) 0%, rgba(30, 58, 138, 0.85) 50%, rgba(37, 99, 235, 0.80) 100%);
            backdrop-filter: blur(2px);
            z-index: 1;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            max-width: 540px;
            padding: 3rem;
            color: #ffffff;
        }

        .glass-card {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.18);
            border-radius: 16px;
            padding: 1.25rem 1.5rem;
            box-shadow: 0 8px 32px 0 rgba(0, 0, 0, 0.15);
            transition: transform 0.25s ease, background 0.25s ease;
        }

        .glass-card:hover {
            transform: translateY(-3px);
            background: rgba(255, 255, 255, 0.15);
        }

        .feature-icon-badge {
            width: 42px;
            height: 42px;
            border-radius: 10px;
            background: rgba(255, 255, 255, 0.18);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.25rem;
            color: #ffffff;
            flex-shrink: 0;
        }

        .stats-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.45rem 1rem;
            border-radius: 9999px;
            background: rgba(255, 255, 255, 0.15);
            border: 1px solid rgba(255, 255, 255, 0.25);
            color: #ffffff;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.5rem 0;
            color: #94a3b8;
            font-size: 0.825rem;
            font-weight: 500;
        }

        .auth-divider::before,
        .auth-divider::after {
            content: '';
            flex: 1;
            border-bottom: 1px solid #e2e8f0;
        }

        .auth-divider span {
            padding: 0 1rem;
        }

        .link-login-pill {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
            width: 100%;
            height: 48px;
            border-radius: 12px;
            border: 1.5px solid #e2e8f0;
            background-color: #ffffff;
            color: #334155;
            font-weight: 600;
            font-size: 0.925rem;
            text-decoration: none;
            transition: all 0.2s ease;
        }

        .link-login-pill:hover {
            border-color: #2563eb;
            color: #2563eb;
            background-color: #eff6ff;
            transform: translateY(-1px);
        }
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-lg-row auth-container">
        <!-- Left: Register Form -->
        <div class="auth-form-area w-100 w-lg-50">
            <div class="auth-card-inner">
                <!-- Brand Header -->
                <div>
                    <a href="{{ url('/') }}" class="brand-badge">
                        <div class="brand-icon-box">
                            <i class="bi bi-mortarboard-fill"></i>
                        </div>
                        <div class="brand-text">
                            Curso<span>+</span>
                        </div>
                    </a>
                </div>

                <!-- Title -->
                <div class="mb-6">
                    <h1 class="fw-bold text-gray-900 fs-2x mb-2">Crear nueva cuenta 🚀</h1>
                    <p class="text-muted fs-6 fw-medium mb-0">
                        Únete y accede a un catálogo exclusivo de cursos para impulsar tu carrera.
                    </p>
                </div>

                <!-- Registration Form -->
                <form id="registerForm" method="POST" novalidate>
                    @csrf

                    <!-- Full Name -->
                    <div class="custom-input-wrapper">
                        <input type="text" id="name" name="name" class="form-control"
                            placeholder="Nombre completo" autocomplete="name" required />
                        <i class="bi bi-person input-icon-left"></i>
                    </div>

                    <!-- Username -->
                    <div class="custom-input-wrapper">
                        <input type="text" id="usuario" name="usuario" maxlength="20" class="form-control"
                            placeholder="Nombre de usuario" autocomplete="username" required />
                        <i class="bi bi-at input-icon-left"></i>
                    </div>

                    <!-- Email -->
                    <div class="custom-input-wrapper">
                        <input type="email" id="email" name="email" class="form-control"
                            placeholder="Correo electrónico" autocomplete="email" required />
                        <i class="bi bi-envelope input-icon-left"></i>
                    </div>

                    <!-- Password -->
                    <div class="custom-input-wrapper has-toggle">
                        <input type="password" id="password" name="password" class="form-control"
                            placeholder="Contraseña (mínimo 6 caracteres)" autocomplete="new-password" required />
                        <i class="bi bi-lock input-icon-left"></i>
                        <button type="button" class="password-toggle-btn" id="togglePasswordBtn" tabindex="-1" aria-label="Mostrar contraseña">
                            <i class="bi bi-eye" id="togglePasswordIcon"></i>
                        </button>
                    </div>

                    <!-- Confirm Password -->
                    <div class="custom-input-wrapper has-toggle">
                        <input type="password" id="password_confirmation" name="password_confirmation" class="form-control"
                            placeholder="Confirmar contraseña" autocomplete="new-password" required />
                        <i class="bi bi-shield-check input-icon-left"></i>
                        <button type="button" class="password-toggle-btn" id="togglePasswordConfirmBtn" tabindex="-1" aria-label="Mostrar confirmación de contraseña">
                            <i class="bi bi-eye" id="togglePasswordConfirmIcon"></i>
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="mt-6 mb-4">
                        <button type="submit" id="registerButton" class="btn-brand-primary">
                            <span id="btnText">Registrarme ahora</span>
                            <i class="bi bi-arrow-right ms-1"></i>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>¿Ya tienes una cuenta?</span>
                    </div>

                    <!-- Link to Login -->
                    <a href="{{ route('login') }}" class="link-login-pill">
                        <i class="bi bi-box-arrow-in-right"></i>
                        Iniciar sesión
                    </a>
                </form>
            </div>
        </div>

        <!-- Right: Hero Banner -->
        <div class="hero-banner d-none d-lg-flex w-lg-50">
            <div class="hero-overlay"></div>
            <div class="hero-content">
                <div class="mb-5">
                    <span class="stats-pill">
                        <i class="bi bi-stars text-warning"></i> Plataforma Educativa Líder
                    </span>
                </div>

                <h2 class="display-6 fw-bolder mb-4 text-white lh-base">
                    Aprende nuevas habilidades y alcanza tus metas.
                </h2>
                <p class="fs-5 text-white-50 mb-8 lh-lg">
                    Cursos impartidos por instructores capacitados, diseñados para impulsar tu crecimiento profesional día a día.
                </p>

                <!-- Feature Highlights -->
                <div class="d-flex flex-column gap-4">
                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-book-half"></i>
                        </div>
                        <div>
                            <h4 class="fs-6 fw-bold text-white mb-1">Diversidad de Cursos</h4>
                            <p class="fs-7 text-white-50 mb-0">Contenido enfocado en áreas de alta demanda y actualización constante.</p>
                        </div>
                    </div>

                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-person-workspace"></i>
                        </div>
                        <div>
                            <h4 class="fs-6 fw-bold text-white mb-1">Docentes Dedicados</h4>
                            <p class="fs-7 text-white-50 mb-0">Aprende de maestros especializados que guían tu aprendizaje.</p>
                        </div>
                    </div>

                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-credit-card-2-front"></i>
                        </div>
                        <div>
                            <h4 class="fs-6 fw-bold text-white mb-1">Gestión Segura</h4>
                            <p class="fs-7 text-white-50 mb-0">Inscripción rápida, seguimiento de pagos y control total de tus materias.</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Password toggles
        document.getElementById('togglePasswordBtn').addEventListener('click', function () {
            const input = document.getElementById('password');
            const icon = document.getElementById('togglePasswordIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });

        document.getElementById('togglePasswordConfirmBtn').addEventListener('click', function () {
            const input = document.getElementById('password_confirmation');
            const icon = document.getElementById('togglePasswordConfirmIcon');
            if (input.type === 'password') {
                input.type = 'text';
                icon.className = 'bi bi-eye-slash';
            } else {
                input.type = 'password';
                icon.className = 'bi bi-eye';
            }
        });

        // Register form handling
        const BASE_URL = $('meta[name="base-url"]').attr("content").replace(/\/$/, "");
        const CSRF_TOKEN = $('meta[name="csrf-token"]').attr("content");

        $("#registerForm").on("submit", function (e) {
            e.preventDefault();

            const $btn = $("#registerButton");
            const originalContent = $btn.html();
            $btn.prop("disabled", true).html('<span class="spinner-border spinner-border-sm me-2"></span>Registrando...');

            $.ajax({
                url: BASE_URL + "/register",
                type: "POST",
                data: $(this).serialize(),
                headers: {
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                success: function (res) {
                    Swal.fire({
                        icon: "success",
                        title: "¡Cuenta creada exitosamente!",
                        text: res.message || "Tu registro ha sido completado. Te redirigiremos al inicio de sesión.",
                        confirmButtonColor: "#2563eb",
                        timer: 2000,
                        timerProgressBar: true
                    }).then(() => {
                        window.location.href = "{{ route('login') }}";
                    });
                },
                error: function (xhr) {
                    $btn.prop("disabled", false).html(originalContent);
                    if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                        const errors = xhr.responseJSON.errors;
                        let errorMsg = '<ul class="text-start mb-0 ps-4">';
                        for (let k in errors) {
                            errors[k].forEach(msg => {
                                errorMsg += `<li>${msg}</li>`;
                            });
                        }
                        errorMsg += '</ul>';

                        Swal.fire({
                            icon: "error",
                            title: "Corrige los siguientes errores",
                            html: errorMsg,
                            confirmButtonColor: "#2563eb"
                        });
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Error en el servidor",
                            text: (xhr.responseJSON && xhr.responseJSON.message) ? xhr.responseJSON.message : "Ocurrió un error al procesar tu solicitud. Intenta nuevamente.",
                            confirmButtonColor: "#2563eb"
                        });
                    }
                }
            });
        });
    </script>
</body>

</html>
