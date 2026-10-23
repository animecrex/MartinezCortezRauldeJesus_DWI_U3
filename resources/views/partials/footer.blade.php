<footer class="py-4 px-6 border-top bg-white mt-auto text-center text-md-start">
    <div class="container-fluid d-flex flex-column flex-md-row align-items-center justify-content-between gap-2">
        <div class="text-muted fs-7">
            &copy; {{ date('Y') }} <strong class="text-dark">Curso+</strong>. Plataforma Educativa Integral.
        </div>
        <div class="d-flex align-items-center gap-3 fs-7">
            <a href="{{ route('curso') }}" class="text-muted text-hover-primary text-decoration-none">Cursos</a>
            <span class="text-muted">•</span>
            <a href="{{ route('mis-suscripciones') }}" class="text-muted text-hover-primary text-decoration-none">Suscripciones</a>
            <span class="text-muted">•</span>
            <a href="{{ route('vistaperfil') }}" class="text-muted text-hover-primary text-decoration-none">Métodos de Pago</a>
        </div>
    </div>
</footer>