<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="utf-8">
    <title>{{ config('app.name', 'Curso+') }} | Plataforma de Aprendizaje</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="base-url" content="{{ url('/') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="shortcut icon" href="{{ asset('assets/media/logos/favicon.ico') }}" />

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet" />

    <!-- Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Framework & Vendor Styles -->
    <link href="{{ asset('assets/plugins/global/plugins.bundle.css') }}" rel="stylesheet">
    <link href="{{ asset('assets/css/style.bundle.css') }}" rel="stylesheet">

    <!-- Modern App Custom Design System -->
    <link href="{{ asset('assets/css/modern-custom.css?v=2.0') }}" rel="stylesheet">

    @yield('styles')
</head>

<body id="kt_app_body" class="app-default">

    <div class="d-flex flex-column flex-root app-root min-vh-100">
        {{-- HEADER / TOP NAVBAR --}}
        @include('partials.header')

        {{-- MAIN APP CONTAINER WITH SIDEBAR & CONTENT --}}
        <div class="app-wrapper d-flex flex-grow-1">

            {{-- SIDEBAR --}}
            @include('partials.sidebar')

            {{-- MAIN VIEW AREA --}}
            <main class="app-main d-flex flex-column flex-row-fluid w-100 overflow-hidden">
                <div class="app-content flex-column-fluid p-4 p-md-6 p-xl-8">
                    @yield('content')
                </div>

                {{-- FOOTER --}}
                @include('partials.footer')
            </main>

        </div>
    </div>

    <!-- Core Scripts -->
    <script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Global mobile sidebar toggle
        document.addEventListener('DOMContentLoaded', function () {
            const toggleBtn = document.getElementById('sidebarMobileToggle');
            const sidebar = document.getElementById('customAppSidebar');
            if (toggleBtn && sidebar) {
                toggleBtn.addEventListener('click', function () {
                    sidebar.classList.toggle('show');
                });

                // Close sidebar on clicking outside
                document.addEventListener('click', function (e) {
                    if (!sidebar.contains(e.target) && !toggleBtn.contains(e.target) && sidebar.classList.contains('show')) {
                        sidebar.classList.remove('show');
                    }
                });
            }
        });
    </script>

    @yield('javascript')

</body>

</html>
