<!DOCTYPE html>
<html lang="es">
<head>
    <title>Curso+</title>
    <meta charset="utf-8" />
    <meta name="description" content="Sistema de Gestiones" />
    <meta name="keywords" content="gestiones" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <meta property="og:locale" content="es_MX" />
    <meta property="og:type" content="article" />
    <meta property="og:title" content="ATENCIÓN CIUDADANA - Gestiones" />
    <meta property="og:url" content="https://keenthemes.com/metronic" />
    <meta property="og:site_name" content="ATENCIÓN CIUDADANA - Gestiones" />
    <link rel="canonical" href="http://preview.keenthemes.comauthentication/layouts/fancy/sign-in.html" />
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Inter:300,400,500,600,700" />
    
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet" type="text/css" />
    <style>
        .relative.inline-flex.items-center {
            display: none;
        }
    </style>
    <script>
    </script>
</head>

<body id="kt_body" class="app-blank">

    <div class="d-flex flex-column flex-root" id="kt_app_root">
        <div class="d-flex flex-column flex-lg-row flex-column-fluid">
            <div class="d-flex flex-column flex-column-fluid flex-center w-lg-50 p-10">
                <div class="d-flex justify-content-between flex-column-fluid flex-column w-100 mw-450px">
                    <div class="d-flex flex-stack py-2">
                    </div>
                    <div class="py-20">
                        <form method="POST" class="form w-100" novalidate="novalidate" id="kt_sign_in_form"
                            data-kt-redirect-url="#" >
                            @csrf
                            <div class="card-body">
                                <div class="text-start mb-10">
                                    <h1 class="text-gray-900 mb-3 fs-3x" data-kt-translate="sign-in-title">Iniciar
                                        Sesión</h1>
                                </div>
                                @error('erroLogin')
                                    <div class="alert alert-danger d-flex align-items-center p-2">
                                        <i class="ki-duotone ki-shield-cross fs-2hx text-danger me-4">
                                            <span class="path1"></span>
                                            <span class="path2"></span>
                                            <span class="path3"></span>
                                        </i>
                                        <div class="d-flex flex-column">
                                            <span>{{ $message }}</span>
                                        </div>
                                    </div>
                                @enderror
                                <div class="fv-row mb-8">
                                    <input type="text" placeholder="Usuario" name="usuario" id="usuario"
                                        autocomplete="off" autocomplete="off" data-kt-translate="sign-in-input-email"
                                        class="form-control form-control-solid" />
                                </div>
                                <div class="fv-row mb-7">
                                    <input type="password" placeholder="Password" name="password" autocomplete="off"
                                        id="password" data-kt-translate="sign-in-input-password"
                                        class="form-control form-control-solid" />
                                </div>

                                <div class="d-flex flex-stack">
                                    <button id="kt_sign_in_submit" class="btn btn-secondary me-2 flex-shrink-0">
                                        <span class="indicator-label" data-kt-translate="sign-in-submit">Iniciar
                                            Sesión</span>
                                        <span class="indicator-progress">
                                            <span data-kt-translate="general-progress">Por favor espere...</span>
                                            <span class="spinner-border spinner-border-sm align-middle ms-2"></span>
                                        </span>
                                    </button>
                                </div>
                                 <div class="text-gray-500 text-center fw-semibold fs-6">
                                No tienes cuenta?
                                <a href="{{ route('registro') }}" class="link-primary">Registrate</a>
                            </div>
                            </div>
                        </form>
                    </div>
                    
                </div>
            </div>
            <div class="d-flex flex-lg-row-fluid w-lg-50 bgi-size-cover bgi-position-center order-1 order-lg-2"
                style="background-image: url(assets/media/misc/curso.jpg)">
                <div class="d-flex flex-column flex-center py-7 py-lg-15 px-5 px-md-15 w-100">
                    <a href="index.html" class="mb-0 mb-lg-12">

                    </a>
                    <img class="d-none d-lg-block mx-auto w-275px w-md-50 w-xl-500px mb-10 mb-lg-20" src=""
                        alt="" />

                </div>
            </div>
        </div>

        <script>
            var hostUrl = "{{ asset('assets/') }}";
        </script>
        
        <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
        <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
        <script>
            history.pushState(null, null, location.href);

            window.onpopstate = function() {
                history.go(1);
            };
        </script>

</body>

</html>
