@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <h1 class="page-hero-title">Explorar Cursos 🎓</h1>
                <p class="page-hero-subtitle">Descubre y matricúlate en los cursos diseñados para tu desarrollo profesional.</p>
            </div>
            <div class="d-flex align-items-center gap-3">
                <span class="badge-modern-primary">
                    <i class="bi bi-mortarboard-fill me-1"></i> <span id="totalCursosCount">...</span> Cursos en catálogo
                </span>
                <a href="{{ route('crearcurso') }}" class="btn-modern-primary">
                    <i class="bi bi-plus-lg"></i>
                    <span>Crear Curso</span>
                </a>
            </div>
        </div>

        <!-- Filter & Search Bar -->
        <div class="modern-card mb-6">
            <div class="p-4 d-flex flex-column flex-md-row align-items-center justify-content-between gap-3">
                <div class="input-group" style="max-width: 480px;">
                    <span class="input-group-text bg-light border-end-0">
                        <i class="bi bi-search text-muted"></i>
                    </span>
                    <input type="text" id="searchCursosInput" class="form-control form-control-modern border-start-0" 
                           placeholder="Buscar por nombre, docente o materia...">
                </div>

                <div class="d-flex align-items-center gap-2 text-muted fs-7">
                    <i class="bi bi-info-circle"></i>
                    <span>Selecciona cualquier curso para consultar su temario e inscribirte.</span>
                </div>
            </div>
        </div>

        <!-- Courses Cards Grid -->
        <div class="courses-modern-grid" id="contenedor-cursos">
            @include('cursos.cursodis')
        </div>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/js/cursoss/cursos.js?v=2.0.0') }}"></script>
@endsection
