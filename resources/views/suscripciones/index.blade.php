@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <h1 class="page-hero-title">Mis Suscripciones 🎓</h1>
                <p class="page-hero-subtitle">Cursos en los que te encuentras inscrito actualmente.</p>
            </div>
            <div>
                <a href="{{ route('curso') }}" class="btn-modern-primary">
                    <i class="bi bi-search"></i>
                    <span>Explorar Más Cursos</span>
                </a>
            </div>
        </div>

        @if($cursos->count() > 0)
            <div class="courses-modern-grid">
                @foreach($cursos as $curso)
                    <div class="course-card">
                        <div class="course-card-img-wrapper">
                            @if($curso->imagen)
                                <img src="{{ asset('storage/' . $curso->imagen) }}" class="course-card-img" alt="{{ $curso->nombre }}">
                            @else
                                <img src="{{ asset('assets/media/misc/curso.jpg') }}" class="course-card-img" alt="{{ $curso->nombre }}">
                            @endif
                            <span class="badge-modern-success position-absolute" style="top: 1rem; right: 1rem; background: rgba(16, 185, 129, 0.9); color: white; border: none;">
                                <i class="bi bi-check-circle-fill me-1"></i> Inscrito
                            </span>
                        </div>

                        <div class="course-card-body">
                            <h3 class="course-card-title">{{ $curso->nombre }}</h3>
                            <p class="course-card-desc">{{ $curso->descripcion ?: 'Curso formativo activo.' }}</p>

                            <div class="mb-4">
                                <div class="course-meta-item">
                                    <i class="bi bi-person-workspace text-primary"></i>
                                    <span>{{ $curso->maestro ?: 'Docente Asignado' }}</span>
                                </div>
                                <div class="course-meta-item">
                                    <i class="bi bi-clock-history text-warning"></i>
                                    <span>{{ $curso->horas ?: 1 }} horas</span>
                                </div>
                                <div class="course-meta-item">
                                    <i class="bi bi-calendar-event text-info"></i>
                                    <span>Inicio: {{ $curso->fecha_inicio ? date('d/m/Y', strtotime($curso->fecha_inicio)) : 'Próximamente' }}</span>
                                </div>
                            </div>

                            <div class="course-card-footer">
                                <a href="{{ route('detallescurso', \Illuminate\Support\Facades\Crypt::encryptString($curso->id)) }}" 
                                   class="btn-modern-outline w-100 justify-content-center text-decoration-none">
                                    <i class="bi bi-book-half"></i>
                                    <span>Ir al Aula & Detalles</span>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <!-- Empty State Card -->
            <div class="modern-card text-center p-6 p-md-12">
                <div class="d-inline-flex align-items-center justify-content-center rounded-circle p-4 mb-4" 
                     style="background: #eff6ff; width: 90px; height: 90px;">
                    <i class="bi bi-mortarboard fs-2hx text-primary"></i>
                </div>
                <h2 class="fw-bold text-dark fs-2 mb-2">Aún no estás inscrito en ningún curso</h2>
                <p class="text-muted fs-6 mx-auto mb-6" style="max-width: 480px;">
                    Explora nuestro catálogo con decenas de materias disponibles impartidas por instructores expertos.
                </p>
                <a href="{{ route('curso') }}" class="btn-modern-primary py-3 px-5 fs-6">
                    <i class="bi bi-compass me-1"></i> Explorar Catálogo de Cursos
                </a>
            </div>
        @endif
    </div>
@endsection