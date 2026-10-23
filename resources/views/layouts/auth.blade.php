<!DOCTYPE html>

<html lang="es">
<head>
    <meta charset="utf-8">
    <title>Mi Sistema</title>

```
<!-- METRONIC CSS -->
<link rel="stylesheet" href="{{ asset('assets/plugins/global/plugins.bundle.css') }}">
<link rel="stylesheet" href="{{ asset('assets/css/style.bundle.css') }}">
```

</head>

<body>

```
<!-- CONTENIDO DINÁMICO -->
@yield('content')

<!-- SCRIPTS -->
<script>
    var hostUrl = "{{ asset('assets/') }}";
</script>

<script src="{{ asset('assets/plugins/global/plugins.bundle.js') }}"></script>
<script src="{{ asset('assets/js/scripts.bundle.js') }}"></script>
```

</body>
</html>
