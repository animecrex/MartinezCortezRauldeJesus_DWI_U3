@extends('layouts.app')

@section('content')
    <div class="d-flex flex-column flex-column-fluid">
        @include('cursos.pagcurso')
    </div>
@endsection

@section('javascript')
    <script src="{{ asset('assets/js/cursoss/cursos.js?v=2.0.0') }}"></script>
@endsection
