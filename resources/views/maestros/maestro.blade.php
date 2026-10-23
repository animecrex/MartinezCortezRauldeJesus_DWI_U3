@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        <!-- Page Hero Banner -->
        <div class="page-hero-banner">
            <div>
                <h1 class="page-hero-title">Gestión de Maestros 👨‍🏫</h1>
                <p class="page-hero-subtitle">Registra nuevos docentes y consulta el claustro de profesores activos.</p>
            </div>
            <div>
                <span class="badge-modern-primary">
                    <i class="bi bi-people-fill me-1"></i> {{ count($maestros) }} Maestros registrados
                </span>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success d-flex align-items-center gap-3 p-4 rounded-4 shadow-sm mb-6 border-0 bg-light-success">
                <i class="bi bi-check-circle-fill text-success fs-3"></i>
                <div>
                    <h5 class="fw-bold text-success mb-0">¡Registro Exitoso!</h5>
                    <span class="text-success fs-7">{{ session('success') }}</span>
                </div>
            </div>
        @endif

        <form action="{{ route('maestros.guardar') }}" method="POST" autocomplete="off">
            @csrf
            @include('maestros.index')
        </form>
    </div>
@endsection