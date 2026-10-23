<!DOCTYPE html>
<html lang="es">

<head>
    <title>Iniciar Sesión | Curso+</title>
    <meta charset="utf-8" />
    <meta name="description" content="Plataforma de cursos y aprendizaje virtual Curso+" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="es_MX" />
    <meta property="og:type" content="website" />
    <meta property="og:title" content="Curso+ | Iniciar Sesión" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Core Theme Styles -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <!-- Bootstrap Icons CDN for guaranteed crisp icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css" />

    <style>
        :root {
            --brand-primary: #2563eb;
            --brand-primary-hover: #1d4ed8;
            --brand-gradient: linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%);
            --brand-gradient-glow: linear-gradient(135deg, #3b82f6 0%, #1e40af 100%);
        }

        body {
            font-family: 'Plus Jakarta Sans', 'Inter', system-ui, -apple-system, sans-serif;
            background-color: #f8fafc;
            color: #1e293b;
            min-height: 100vh;
            overflow-x: hidden;
        }

        .login-container {
            min-height: 100vh;
        }

        /* Form Column Styling */
        .login-form-area {
            background-color: #ffffff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            padding: 2.5rem 1.5rem;
            position: relative;
        }

        @media (min-width: 992px) {
            .login-form-area {
                padding: 3rem 4rem;
            }
        }

        .login-card-inner {
            width: 100%;
            max-width: 440px;
        }

        /* Logo Brand Style */
        .brand-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.75rem;
            text-decoration: none;
            margin-bottom: 2rem;
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
            margin-bottom: 1.35rem;
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
            height: 52px;
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

        /* Custom Alert */
        .alert-auth {
            border-radius: 12px;
            padding: 0.85rem 1rem;
            border: 1px solid #fee2e2;
            background-color: #fef2f2;
            color: #991b1b;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-6px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
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
        }

        .hero-overlay {
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(15, 23, 42, 0.90) 0%, rgba(30, 58, 138, 0.82) 50%, rgba(37, 99, 235, 0.80) 100%);
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

        /* Glassmorphism Feature Card */
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

        /* Divider */
        .auth-divider {
            display: flex;
            align-items: center;
            text-align: center;
            margin: 1.75rem 0;
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

        .link-register-pill {
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

        .link-register-pill:hover {
            border-color: #2563eb;
            color: #2563eb;
            background-color: #eff6ff;
            transform: translateY(-1px);
        }

        /* Stats Badge */
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
    </style>
</head>

<body>
    <div class="d-flex flex-column flex-lg-row login-container">
        
        <!-- Left: Login Form Section -->
        <div class="login-form-area w-100 w-lg-50">
            <div class="login-card-inner">
                
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

                <!-- Welcome Text -->
                <div class="mb-8">
                    <h1 class="fw-bold text-gray-900 fs-2x mb-2">¡Bienvenido de nuevo! 👋</h1>
                    <p class="text-muted fs-6 fw-medium mb-0">
                        Ingresa tus credenciales para acceder a tus cursos y continuar aprendiendo.
                    </p>
                </div>

                <!-- Error & Status Alerts -->
                @if ($errors->any())
                    <div class="alert-auth">
                        <i class="bi bi-shield-x text-danger fs-4 flex-shrink-0"></i>
                        <div class="d-flex flex-column">
                            @if ($errors->has('erroLogin'))
                                <span class="fw-bold">{{ $errors->first('erroLogin') }}</span>
                            @elseif ($errors->has('error'))
                                <span class="fw-bold">{{ $errors->first('error') }}</span>
                            @else
                                <span class="fw-bold">{{ $errors->first() }}</span>
                            @endif
                            <small class="text-muted mt-0.5">Por favor verifica los datos ingresados.</small>
                        </div>
                    </div>
                @endif

                @if (session('status'))
                    <div class="alert alert-success d-flex align-items-center p-3 mb-6 rounded-3 border-0 bg-light-success">
                        <i class="bi bi-check-circle-fill fs-4 text-success me-3"></i>
                        <span class="text-success fs-7 fw-semibold">{{ session('status') }}</span>
                    </div>
                @endif

                <!-- Login Form -->
                <form method="POST" action="{{ route('login') }}" class="form w-100" id="kt_sign_in_form" autocomplete="on">
                    @csrf

                    <!-- Usuario Input -->
                    <div class="custom-input-wrapper">
                        <label for="usuario" class="form-label fs-7 fw-bold text-gray-700 mb-1 d-block">
                            Usuario o Nombre de Cuenta
                        </label>
                        <div class="position-relative">
                            <input type="text" 
                                id="usuario" 
                                name="usuario" 
                                class="form-control @error('erroLogin') is-invalid @enderror" 
                                placeholder="Ingresa tu usuario" 
                                value="{{ old('usuario') }}" 
                                required 
                                autofocus 
                                autocomplete="username" />
                            <i class="bi bi-person-fill input-icon-left"></i>
                        </div>
                    </div>

                    <!-- Password Input -->
                    <div class="custom-input-wrapper has-toggle mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label fs-7 fw-bold text-gray-700 mb-0">
                                Contraseña
                            </label>
                            @if (Route::has('password.request'))
                                <a href="{{ route('password.request') }}" class="fs-7 fw-semibold text-primary text-decoration-none text-hover-primary">
                                    ¿Olvidaste tu contraseña?
                                </a>
                            @endif
                        </div>
                        <div class="position-relative">
                            <input type="password" 
                                id="password" 
                                name="password" 
                                class="form-control @error('erroLogin') is-invalid @enderror" 
                                placeholder="••••••••" 
                                required 
                                autocomplete="current-password" />
                            <i class="bi bi-lock-fill input-icon-left"></i>
                            <button type="button" class="password-toggle-btn" id="togglePasswordBtn" title="Mostrar/ocultar contraseña" tabindex="-1">
                                <i class="bi bi-eye" id="togglePasswordIcon"></i>
                            </button>
                        </div>
                    </div>

                    <!-- Remember Me Option -->
                    <div class="d-flex align-items-center justify-content-between mb-7">
                        <div class="form-check form-check-custom form-check-solid">
                            <input class="form-check-input h-18px w-18px" type="checkbox" name="remember" id="remember_me" />
                            <label class="form-check-label text-gray-600 fs-7 fw-medium ms-2 cursor-pointer" for="remember_me">
                                Recordar mi sesión
                            </label>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="mb-6">
                        <button type="submit" id="kt_sign_in_submit" class="btn btn-brand-primary">
                            <span class="indicator-label d-flex align-items-center justify-content-center gap-2" id="submitLabel">
                                <span>Iniciar Sesión</span>
                                <i class="bi bi-arrow-right fs-5"></i>
                            </span>
                            <span class="indicator-progress d-none" id="submitProgress">
                                <span>Iniciando sesión...</span>
                                <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                            </span>
                        </button>
                    </div>

                    <!-- Divider -->
                    <div class="auth-divider">
                        <span>¿Nuevo en Curso+?</span>
                    </div>

                    <!-- Register Link -->
                    <div>
                        <a href="{{ route('registro') }}" class="link-register-pill">
                            <i class="bi bi-person-plus text-primary fs-5"></i>
                            <span>Crear una cuenta nueva</span>
                        </a>
                    </div>
                </form>

                <!-- Footer info -->
                <div class="text-center text-muted fs-8 mt-10">
                    &copy; {{ date('Y') }} Curso+. Todos los derechos reservados.
                </div>

            </div>
        </div>

        <!-- Right: Modern Hero Showcase Banner -->
        <div class="d-none d-lg-flex w-lg-50 hero-banner" style="background-image: url('{{ asset('assets/media/misc/curso.jpg') }}');">
            <div class="hero-overlay"></div>
            
            <div class="hero-content">
                <!-- Top pill badge -->
                <div class="mb-5">
                    <span class="stats-pill">
                        <i class="bi bi-patch-check-fill text-warning"></i>
                        Plataforma de Educación Profesional
                    </span>
                </div>

                <!-- Main Hero Heading -->
                <h2 class="display-6 fw-extrabold text-white mb-4 lh-sm">
                    Aprende nuevas habilidades y transforma tu carrera hoy.
                </h2>
                
                <p class="text-white text-opacity-80 fs-5 fw-normal mb-8 lh-base">
                    Accede a cursos completos impartidos por instructores con experiencia real en la industria y obtén certificaciones avaladas.
                </p>

                <!-- Feature Cards Stack -->
                <div class="d-flex flex-column gap-3 mb-8">
                    
                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-play-circle-fill text-warning"></i>
                        </div>
                        <div>
                            <h4 class="text-white fs-6 fw-bold mb-0">Aprende a tu propio ritmo</h4>
                            <p class="text-white text-opacity-75 fs-7 mb-0">Contenido disponible 24/7 desde cualquier dispositivo.</p>
                        </div>
                    </div>

                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-award-fill text-info"></i>
                        </div>
                        <div>
                            <h4 class="text-white fs-6 fw-bold mb-0">Certificados de finalización</h4>
                            <p class="text-white text-opacity-75 fs-7 mb-0">Demuestra tus competencias y enriquece tu currículum.</p>
                        </div>
                    </div>

                    <div class="glass-card d-flex align-items-center gap-3">
                        <div class="feature-icon-badge">
                            <i class="bi bi-people-fill text-success"></i>
                        </div>
                        <div>
                            <h4 class="text-white fs-6 fw-bold mb-0">Docentes calificados</h4>
                            <p class="text-white text-opacity-75 fs-7 mb-0">Aprende directamente de profesionales destacados.</p>
                        </div>
                    </div>

                </div>

                <!-- Social Proof Rating -->
                <div class="d-flex align-items-center gap-4 pt-4 border-top border-white border-opacity-15">
                    <div class="d-flex align-items-center gap-1 text-warning fs-6">
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                        <i class="bi bi-star-fill"></i>
                    </div>
                    <div class="text-white text-opacity-90 fs-7 fw-semibold">
                        4.9 / 5 estrellas en satisfacción estudiantil
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>

    <script>
        // Password toggle visibility logic
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('togglePasswordBtn');
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('togglePasswordIcon');

            if (toggleBtn && passwordInput && toggleIcon) {
                toggleBtn.addEventListener('click', function () {
                    const isPassword = passwordInput.getAttribute('type') === 'password';
                    passwordInput.setAttribute('type', isPassword ? 'text' : 'password');
                    
                    if (isPassword) {
                        toggleIcon.classList.remove('bi-eye');
                        toggleIcon.classList.add('bi-eye-slash');
                    } else {
                        toggleIcon.classList.remove('bi-eye-slash');
                        toggleIcon.classList.add('bi-eye');
                    }
                });
            }

            // Form submit loading state
            const form = document.getElementById('kt_sign_in_form');
            const submitBtn = document.getElementById('kt_sign_in_submit');
            const submitLabel = document.getElementById('submitLabel');
            const submitProgress = document.getElementById('submitProgress');

            if (form && submitBtn) {
                form.addEventListener('submit', function () {
                    if (form.checkValidity()) {
                        submitBtn.setAttribute('disabled', 'true');
                        if (submitLabel) submitLabel.classList.add('d-none');
                        if (submitProgress) submitProgress.classList.remove('d-none');
                    }
                });
            }

            // Prevent history back loop if previously logged in
            history.pushState(null, null, location.href);
            window.onpopstate = function () {
                history.go(1);
            };
        });
    </script>
</body>

</html>
