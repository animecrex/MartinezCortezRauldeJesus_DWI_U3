@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Header -->
        <div class="page-hero-banner">
            <div>
                <h1 class="page-hero-title">Gestión y Creación de Cursos 📚</h1>
                <p class="page-hero-subtitle">Diseña nuevos programas educativos y gestiona el contenido de tus materias.</p>
            </div>
            <div>
                <a href="{{ route('curso') }}" class="btn-modern-outline">
                    <i class="bi bi-grid-fill"></i>
                    <span>Ver Catálogo Público</span>
                </a>
            </div>
        </div>

        <!-- Form & List Container -->
        <form id="registrarcurso" method="POST" enctype="multipart/form-data" autocomplete="off">
            @csrf

            <!-- Form Card Component -->
            <div class="modern-card mb-6">
                @include('crearcurso.crearcurs')

                <div class="p-4 p-md-6 bg-light-subtle border-top d-flex justify-content-end gap-3">
                    <button type="reset" class="btn-modern-outline">
                        <i class="bi bi-arrow-counterclockwise"></i>
                        <span>Limpiar Campos</span>
                    </button>
                    <button type="submit" id="guardar_curso" class="btn-modern-primary">
                        <i class="bi bi-cloud-arrow-up-fill"></i>
                        <span>Publicar Curso</span>
                    </button>
                </div>
            </div>

            <!-- Existing Courses Table Card Component -->
            @include('crearcurso.listadecurso')
        </form>
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/plugins/custom/datatables/datatables.bundle.js') }}"></script>
    <script src="{{ asset('assets/js/cursos/curso.js?v=2.0.0') }}"></script>
@endsection
