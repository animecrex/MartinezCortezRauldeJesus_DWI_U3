# Documentación Técnica del Proyecto: Plataforma Curso+ (DWI - Unidad 3)

**Alumno:** Raúl de Jesús Martínez Cortez  
**Materia:** Desarrollo Web Integral (DWI) - Unidad 3  
**Proyecto:** Plataforma Web de Gestión y Suscripción de Cursos (**Curso+**)  
**Framework:** Laravel 9.x / PHP 8.2 / MariaDB (MySQL) / RESTful Web Services  
**Servidor de Aplicaciones:** XAMPP (Apache HTTP Server 2.4.58 / PHP 8.2.12 / MariaDB 10.4.32)  

---

## 1. Funcionamiento del Sitio Web

El sistema **Curso+** es una aplicación web integral que permite a los usuarios autenticados explorar un catálogo de cursos, consultar precios en Pesos Mexicanos (MXN) y su conversión en tiempo real a divisas internacionales (USD, EUR, etc.), administrar métodos de pago (tarjetas bancarias), inscribirse o desinscribirse de cursos verificando el cupo disponible, así como registrar nuevos cursos y profesores.

### 1.1 Inicio de Sesión y Registro de Usuarios
El sistema cuenta con un módulo de acceso e inscripción de nuevos usuarios mediante credenciales seguras (`email` / `usuario` y contraseña).

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.1]**  
> *Coloca aquí la captura de la pantalla de Login (`/login`) y/o Registro de usuario (`/registro`).*  
> ![Captura 1.1 - Login y Registro](ruta/a/tu/captura_login.png)

---

### 1.2 Catálogo Principal de Cursos (`/curso`)
Muestra todos los cursos disponibles en la plataforma, incluyendo imagen representativa, nombre del curso, instructor asignado, horas de duración, costo y acceso al detalle de cada curso.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.2]**  
> *Coloca aquí la captura del catálogo general de cursos (`/curso`).*  
> ![Captura 1.2 - Catálogo de Cursos](ruta/a/tu/captura_catalogo_cursos.png)

---

### 1.3 Detalle del Curso e Inscripción (`/detallescurso/{hash}`)
Presenta la información completa del curso seleccionado (objetivos, requisitos, fechas de inicio y fin, cupo disponible, precio en MXN y conversión de divisas) y permite al alumno suscribirse seleccionando una tarjeta bancaria registrada o cancelar su suscripción.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.3]**  
> *Coloca aquí la captura de la vista de detalles del curso e inscripción (`/detallescurso/...`).*  
> ![Captura 1.3 - Detalles e Inscripción](ruta/a/tu/captura_detalles_curso.png)

---

### 1.4 Creación y Administración de Cursos (`/crearcurso`)
Permite a los usuarios registrar nuevos cursos especificando título, descripción, cupo máximo de alumnos, maestro asignado, total de horas, costo, periodo de fechas, objetivos, requisitos e imagen de portada, además de listar y eliminar los cursos propios.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.4]**  
> *Coloca aquí la captura del módulo de creación y listado de cursos (`/crearcurso`).*  
> ![Captura 1.4 - Crear Curso](ruta/a/tu/captura_crear_curso.png)

---

### 1.5 Mis Suscripciones (`/mis-suscripciones`)
Muestra el listado personalizado de todos los cursos en los cuales el usuario autenticado se encuentra inscrito actualmente.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.5]**  
> *Coloca aquí la captura del apartado Mis Suscripciones (`/mis-suscripciones`).*  
> ![Captura 1.5 - Mis Suscripciones](ruta/a/tu/captura_mis_suscripciones.png)

---

### 1.6 Gestión de Maestros (`/maestros`)
Permite dar de alta nuevos profesores (nombre, apellidos, correo electrónico único, materia principal y turno matutino/vespertino) y visualizar la plantilla docente registrada.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.6]**  
> *Coloca aquí la captura del módulo de Maestros (`/maestros`).*  
> ![Captura 1.6 - Módulo de Maestros](ruta/a/tu/captura_maestros.png)

---

### 1.7 Perfil de Usuario y Gestión de Tarjetas (`/vistaperfil`)
Permite al usuario administrar sus tarjetas bancarias asociadas (BBVA, Banco Azteca, Banamex, Banorte) para el pago de suscripciones y editar su información de perfil.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.7]**  
> *Coloca aquí la captura del perfil y billetera de tarjetas (`/vistaperfil`).*  
> ![Captura 1.7 - Perfil y Tarjetas](ruta/a/tu/captura_perfil_tarjetas.png)

---

### 1.8 Servidor de Aplicaciones (`/servidor-aplicaciones`)
Panel integrado en la barra lateral (**Infraestructura → Servidor de Aplicaciones**) que muestra en tiempo real las especificaciones del servidor HTTP (Apache/PHP), núcleo Laravel, estado y latencia de conexión con MariaDB/MySQL, conteo en vivo de registros, verificación de middlewares de seguridad, bitácoras (`storage/logs/`), botón para limpiar caché del servidor y consola interactiva para probar los endpoints `/api/v1`.

> **[ESPACIO PARA CAPTURA DE PANTALLA 1.8]**  
> *Coloca aquí la captura del módulo Servidor de Aplicaciones dentro del sitio web (`/servidor-aplicaciones`).*  
> ![Captura 1.8 - Servidor de Aplicaciones](ruta/a/tu/captura_servidor_aplicaciones.png)

---

## 2. Descripción de los Mecanismos de Seguridad Empleados

La plataforma implementa múltiples capas de seguridad tanto en el backend como en el frontend para proteger la integridad de la información, la sesión del usuario y los recursos del servidor:

### 2.1 Autenticación de Usuarios y Hashing de Contraseñas (Bcrypt)
- **Descripción:** Todas las contraseñas de los usuarios se almacenan cifradas mediante el algoritmo de hashing unidireccional **Bcrypt** (`Hash::make()`) a través de Laravel Breeze y el modelo `User`. Además, las rutas internas del sistema están protegidas por los middlewares `auth` y `verified` en `routes/web.php`, redirigiendo cualquier intento de acceso no autenticado a la pantalla de `/login`.

> **[ESPACIO PARA CAPTURA DE PANTALLA 2.1]**  
> *Coloca aquí la captura demostrando la protección de rutas (redirección al login cuando no hay sesión) y/o las contraseñas cifradas con Bcrypt en la tabla `users` de la base de datos.*  
> ![Captura 2.1 - Autenticación y Hashing Bcrypt](ruta/a/tu/captura_seguridad_auth.png)

---

### 2.2 Cifrado Simétrico de Identificadores en URLs (`Crypt` AES-256-CBC)
- **Descripción:** Para prevenir ataques de **Enumeración de Recursos (IDOR - Insecure Direct Object Reference)** al consultar detalles o eliminar cursos, los identificadores numéricos (`id`) se cifran mediante el facade `Crypt::encryptString($curso->id)` (cifrado simétrico AES-256-CBC firmado con HMAC). Al recibir la petición en `/detallescurso/{id}` o `/crearcurso/eliminarcurso/{id}`, el controlador desencripta el token con `Crypt::decryptString($id)` y devuelve error `404` o `400` si el hash fue alterado.

> **[ESPACIO PARA CAPTURA DE PANTALLA 2.2]**  
> *Coloca aquí la captura de la barra de direcciones o respuesta JSON donde se observa el hash encriptado en la URL (`/detallescurso/eyJpdiI6...`) en lugar de un ID numérico secuencial.*  
> ![Captura 2.2 - Cifrado de IDs en URL con Crypt](ruta/a/tu/captura_seguridad_crypt.png)

---

### 2.3 Protección contra Falsificación de Peticiones en Sitios Cruzados (CSRF)
- **Descripción:** Todos los formularios `POST`, `PUT`, `PATCH` y `DELETE` incluyen el token criptográfico `@csrf` validado por el middleware `VerifyCsrfToken`. Asimismo, las peticiones asíncronas (Fetch / AJAX) envían la cabecera `X-CSRF-TOKEN` para impedir que sitios externos ejecuten acciones en nombre del usuario autenticado.

> **[ESPACIO PARA CAPTURA DE PANTALLA 2.3]**  
> *Coloca aquí la captura del inspector de elementos o pestaña Network mostrando el campo oculto `_token` o la cabecera `X-CSRF-TOKEN` en las peticiones POST.*  
> ![Captura 2.3 - Token CSRF](ruta/a/tu/captura_seguridad_csrf.png)

---

### 2.4 Enmascaramiento de Datos Sensibles (Tarjetas Bancarias y CVV) y Control de Autorización (`403 Forbidden`)
- **Descripción:** En `ProfileController` y el modelo `Tarjetas`:
  1. En la vista de perfil únicamente se exponen los **últimos 4 dígitos** del número de tarjeta (`substr($card->numero_tarjeta, -4)`).
  2. El atributo `cvv` está declarado en `$hidden = ['cvv']` dentro del modelo `Tarjetas` y se enmascara como `'***'` al consultar una tarjeta para edición.
  3. Se valida la propiedad del recurso (`if ($tarjeta->usuario_id !== Auth::id()) abort(403);` y `where('user_id', Auth::id())`), garantizando que ningún usuario pueda visualizar, editar o eliminar tarjetas o cursos pertenecientes a otro usuario.

> **[ESPACIO PARA CAPTURA DE PANTALLA 2.4]**  
> *Coloca aquí la captura de la vista de tarjetas donde se aprecia el enmascaramiento de los dígitos (`**** 1234` y CVV `***`) o la respuesta de seguridad.*  
> ![Captura 2.4 - Enmascaramiento de Tarjetas y CVV](ruta/a/tu/captura_seguridad_tarjetas.png)

---

### 2.5 Validación Estricta de Datos, Sanitización XSS (`strip_tags`) y Subida Segura de Imágenes
- **Descripción:**
  - **Prevención de XSS e Inyección SQL:** Se utilizan consultas parametrizadas mediante **Eloquent ORM** y limpieza de etiquetas HTML con `strip_tags()` al registrar cursos, además del escape automático `{{ }}` de Blade.
  - **Validación de Entradas y Subida de Archivos:** Se validan expresiones regulares (formato de nombres, expiración `MM/AA`, longitud de tarjetas de 13 a 19 dígitos) y en la subida de imágenes (`CrearcursoController`) se restringe el tipo MIME exclusivamente a `jpeg, png, jpg, webp` con máximo de `2MB`, renombrando el archivo con un hash aleatorio de 32 caracteres (`Str::random(32)`) y procesándolo con `Intervention Image` para eliminar metadatos maliciosos o scripts ocultos.

> **[ESPACIO PARA CAPTURA DE PANTALLA 2.5]**  
> *Coloca aquí la captura mostrando las validaciones de formularios en acción (mensajes de error ante datos inválidos) o el almacenamiento seguro de imágenes renombradas.*  
> ![Captura 2.5 - Validaciones y Sanitización](ruta/a/tu/captura_seguridad_validacion.png)

---

## 3. WEB Services de Terceros Empleados

La plataforma consume un **Servicio Web RESTful externo en tiempo real** para obtener las tasas de cambio internacionales y convertir automáticamente el costo de los cursos de Pesos Mexicanos (**MXN**) a divisas extranjeras (**USD, EUR, GBP, CAD, JPY**).

### 3.1 Ficha Técnica del Web Service de Terceros
- **Proveedor:** Open Exchange Rates API (ExchangeRate-API)
- **URL del Endpoint Externo:** `https://open.er-api.com/v6/latest/MXN`
- **Protocolo y Formato:** `HTTPS` (`GET`) con respuesta en formato `JSON`
- **Clase de Integración en el Proyecto:** `App\Services\ExchangeRateService` (`app/Services/ExchangeRateService.php`)
- **Características de Implementación:**
  - Consumo mediante el cliente HTTP de Laravel (`Http::timeout(4)->acceptJson()->get(...)`).
  - **Optimización con Caché:** Almacena las tasas en caché durante 1 hora (`Cache::remember('open_exchange_rates_mxN', 3600, ...)`) para mejorar el tiempo de respuesta y evitar saturar el límite de peticiones del proveedor.
  - **Tolerancia a Fallos (Fallback):** En caso de que no exista conexión a Internet, el servicio conmuta automáticamente a tasas de respaldo locales sin interrumpir el funcionamiento del sitio web.

---

### 3.2 Demostración del Consumo de Tasas de Cambio en Tiempo Real (`/api/v1/tipo-cambio`)
Este endpoint consulta el servicio externo de Open Exchange Rates y devuelve el listado actualizado de divisas tomando como moneda base el Peso Mexicano (`MXN`).

> **[ESPACIO PARA CAPTURA DE PANTALLA 3.1]**  
> *Coloca aquí la captura de la petición a `GET /api/v1/tipo-cambio` (o en la interfaz web / Postman / navegador) mostrando la respuesta JSON con las tasas de cambio en tiempo real de Open Exchange Rates API.*  
> ![Captura 3.1 - Web Service de Terceros Tasas de Cambio](ruta/a/tu/captura_ws_terceros_tasas.png)

---

### 3.3 Demostración de Conversión Monetaria Automática (`/api/v1/tipo-cambio/convertir` y Precios en Cursos)
Permite convertir cualquier monto en MXN a la moneda solicitada (por ejemplo: `/api/v1/tipo-cambio/convertir?monto=1500&moneda=USD`), además de integrarse directamente dentro de la consulta de cursos para mostrar el equivalente en Dólares (`USD`) y Euros (`EUR`).

> **[ESPACIO PARA CAPTURA DE PANTALLA 3.2]**  
> *Coloca aquí la captura de la conversión de divisas en funcionamiento (por ejemplo, `/api/v1/tipo-cambio/convertir?monto=1500&moneda=USD` o la visualización de precios convertidos a USD/EUR dentro del sistema).*  
> ![Captura 3.2 - Conversión de Divisas con Web Service de Terceros](ruta/a/tu/captura_ws_terceros_conversion.png)

---

## 4. WEB Services Propios Empleados

Se diseñó e implementó una **API RESTful propia versionada (`/api/v1`)** definida en `routes/api.php` y estructurada mediante los controladores del namespace `App\Http\Controllers\Api` (`ApiController`, `ApiDocsController`, `CursoApiController`, `MaestroApiController` y `TipoCambioApiController`). Todas las respuestas siguen un estándar JSON uniforme con códigos de estado HTTP semánticos (`200`, `404`, `422`), marcas de tiempo (`timestamp`) y metadatos.

### Resumen de Endpoints Propios Desarrollados

| Método | Endpoint | Controlador | Descripción |
| :--- | :--- | :--- | :--- |
| `GET` | `/api/v1` | `ApiDocsController@info` | Catálogo general y especificación técnica de todos los endpoints de la API. |
| `GET` | `/api/v1/health` | `ApiDocsController@health` | Health-Check del estado de la API, conexión a BD y estado del Web Service externo. |
| `GET` | `/api/v1/cursos` | `CursoApiController@index` | Lista y filtra cursos (`buscar`, `precio_max`, `orden_por`, `limite`) con precios en MXN/USD y estado del cupo. |
| `GET` | `/api/v1/cursos/{id}` | `CursoApiController@show` | Detalle completo de un curso específico con tarifas en MXN, USD y EUR. |
| `GET` | `/api/v1/cursos/{id}/disponibilidad` | `CursoApiController@disponibilidad` | Verifica en tiempo real el cupo máximo, inscritos actuales, lugares disponibles y porcentaje de ocupación. |
| `GET` | `/api/v1/maestros` | `MaestroApiController@index` | Lista los profesores registrados junto con el total de cursos que imparte cada uno. |
| `GET` | `/api/v1/maestros/{id}` | `MaestroApiController@show` | Consulta el perfil de un maestro específico y el listado de cursos asignados. |

---

### 4.1 Catálogo de Especificación y Estado de Salud (`/api/v1` y `/api/v1/health`)
Proporciona autodescubrimiento de la API y monitoreo en tiempo real del estado de la base de datos MySQL, versión de Laravel/PHP y conectividad con el proveedor de divisas.

> **[ESPACIO PARA CAPTURA DE PANTALLA 4.1]**  
> *Coloca aquí la captura de la respuesta JSON del endpoint `GET /api/v1` o `GET /api/v1/health`.*  
> ![Captura 4.1 - Endpoint Info y Health Check](ruta/a/tu/captura_ws_propio_health.png)

---

### 4.2 Servicio Web Propio de Catálogo y Búsqueda de Cursos (`GET /api/v1/cursos`)
Permite consultar todos los cursos registrados aplicando filtros dinámicos por parámetros query (`?buscar=...&precio_max=...&orden_por=costo&direccion=asc`), calculando de forma automática los lugares disponibles y el precio en USD.

> **[ESPACIO PARA CAPTURA DE PANTALLA 4.2]**  
> *Coloca aquí la captura de la respuesta JSON del endpoint `GET /api/v1/cursos`.*  
> ![Captura 4.2 - Web Service Propio Listado de Cursos](ruta/a/tu/captura_ws_propio_cursos.png)

---

### 4.3 Servicio Web Propio de Detalle y Disponibilidad de Curso (`GET /api/v1/cursos/{id}` y `/disponibilidad`)
Entrega la ficha técnica detallada de un curso por su ID (incluyendo tarifas en MXN, USD y EUR) y cuenta con un sub-recurso especializado (`/api/v1/cursos/{id}/disponibilidad`) que informa el porcentaje de ocupación y si aún admite inscripciones.

> **[ESPACIO PARA CAPTURA DE PANTALLA 4.3]**  
> *Coloca aquí la captura de la respuesta JSON de `GET /api/v1/cursos/{id}` y/o `GET /api/v1/cursos/{id}/disponibilidad`.*  
> ![Captura 4.3 - Web Service Propio Detalle y Disponibilidad](ruta/a/tu/captura_ws_propio_disponibilidad.png)

---

### 4.4 Servicio Web Propio de Consulta de Maestros y Carga Académica (`GET /api/v1/maestros` y `/api/v1/maestros/{id}`)
Expone el directorio de profesores registrados y contabiliza e incluye dinámicamente los cursos que imparte cada docente en la plataforma.

> **[ESPACIO PARA CAPTURA DE PANTALLA 4.4]**  
> *Coloca aquí la captura de la respuesta JSON de `GET /api/v1/maestros` y/o `GET /api/v1/maestros/{id}`.*  
> ![Captura 4.4 - Web Service Propio Maestros](ruta/a/tu/captura_ws_propio_maestros.png)

---

## 5. Servidor de Aplicaciones

La plataforma **Curso+** opera sobre una arquitectura de servidor de aplicaciones basada en el stack **XAMPP** para Windows, integrando **Apache HTTP Server 2.4.58**, el motor de ejecución **PHP 8.2.12 (Zend Engine v4.2.12)**, el núcleo de aplicación **Laravel 9.x** mediante su *Front Controller* (`public/index.php`) y el gestor de base de datos relacional **MariaDB 10.4.32** (compatible con MySQL). Adicionalmente, el proyecto admite ejecución directa en entorno local mediante el servidor embebido de PHP a través de `php artisan serve`.

### Ficha Técnica del Servidor de Aplicaciones

| Componente | Especificación y Configuración |
| :--- | :--- |
| **Stack / Entorno de Despliegue** | XAMPP sobre Windows (`C:\xampp\htdocs\MartinezCortezRauldeJesus_DWI_U3`) |
| **Servidor Web / HTTP** | Apache HTTP Server `2.4.58 (Win64)` con módulos `mod_rewrite`, `mod_headers` y `mod_expires` |
| **Motor de Ejecución (Runtime)** | PHP `8.2.12` (Zend Engine `v4.2.12`, ZTS x64) + Composer PSR-4 Autoloader |
| **Framework / Núcleo HTTP** | Laravel `9.x` (`laravel/framework ^9.19`), Front Controller `public/index.php` y `App\Http\Kernel` |
| **Servidor de Base de Datos** | MariaDB `10.4.32` (MySQL compatible), host `127.0.0.1`, puerto `3306`, BD `proyectoalan` |
| **Sesiones, Caché y Bitácoras** | Driver `file` (`SESSION_LIFETIME=120`, `http_only=true`, `same_site=lax`), caché `file` y logs rotativos en `storage/logs/` |
| **Servidor Embebido de Pruebas** | `php artisan serve` (`http://127.0.0.1:8000`) |

---

### 5.1 Arquitectura del Servidor de Aplicaciones y Ciclo de Vida de la Petición
- **Recepción y Enrutamiento en Apache (`.htaccess` y `public/.htaccess`):** El archivo `.htaccess` en la raíz redirige limpiamente las solicitudes hacia el directorio `/public/`, evitando exponer el código fuente y archivos internos. Dentro de `public/.htaccess`, `mod_rewrite` canaliza todas las rutas dinámicas hacia `public/index.php`.
- **Procesamiento en el Núcleo de Laravel (`bootstrap/app.php` y `app/Http/Kernel.php`):** El *Front Controller* inicializa el contenedor de servicios de Laravel y hace pasar cada petición por la pila de middlewares globales (`TrustProxies`, `HandleCors`, `ValidatePostSize`, `TrimStrings`, `SecurityHeaders`, `RequestSecurityLogger`) y los grupos de rutas `web` (sesiones, cookies cifradas, verificación CSRF) o `api` (`throttle:api`).
- **Ejecución de Lógica y Persistencia:** Los controladores procesan la lógica de negocio, interactúan con la base de datos `proyectoalan` mediante Eloquent ORM / PDO sobre MariaDB (`127.0.0.1:3306`) o consumen APIs externas mediante el cliente HTTP de Laravel, devolviendo vistas Blade compiladas o respuestas JSON estandarizadas.

> **[ESPACIO PARA CAPTURA DE PANTALLA 5.1]**  
> *Coloca aquí la captura del Panel de Control de XAMPP mostrando los servicios de Apache y MySQL/MariaDB en ejecución.*  
> ![Captura 5.1 - Panel de Control XAMPP Apache y MySQL](ruta/a/tu/captura_servidor_xampp.png)

---

### 5.2 Configuración, Endurecimiento y Optimización en Apache (`.htaccess`)
El servidor Apache implementa reglas de seguridad y rendimiento configuradas directamente en `.htaccess` y `public/.htaccess`:
- **Bloqueo de archivos confidenciales (`FilesMatch` y `RedirectMatch`):** Deniega el acceso HTTP directo (`403 Forbidden`) a `.env`, `composer.json`, `composer.lock`, `package.json`, `artisan`, `.editorconfig` y directorios `.git`.
- **Deshabilitación de indexación y firma del servidor:** Configura `Options -MultiViews -Indexes`, `ServerSignature Off` y remueve la cabecera `X-Powered-By` para evitar *fingerprinting*.
- **Cabeceras de seguridad a nivel servidor (`mod_headers`):** Inyecta `X-Frame-Options: SAMEORIGIN`, `X-Content-Type-Options: nosniff`, `Referrer-Policy`, `X-XSS-Protection`, `Permissions-Policy` y `X-Permitted-Cross-Domain-Policies`.
- **Caché de archivos estáticos (`mod_expires`):** Define expiración de 1 mes para archivos CSS (`text/css`), JavaScript (`application/javascript`) e imágenes (`image/*`).

```apache
<IfModule mod_rewrite.c>
    Options -MultiViews -Indexes
    RewriteEngine On
    RewriteCond %{REQUEST_FILENAME} !-d
    RewriteCond %{REQUEST_FILENAME} !-f
    RewriteRule ^ index.php [L]
</IfModule>

<FilesMatch "^(\.env|\.git|composer\.(json|lock)|package\.(json|lock)|phpunit\.xml|artisan|\.editorconfig)">
    Require all denied
</FilesMatch>
```

> **[ESPACIO PARA CAPTURA DE PANTALLA 5.2]**  
> *Coloca aquí la captura verificando el servidor de aplicaciones en operación (acceso al sitio en Apache/XAMPP o ejecución de `php artisan serve` junto con las cabeceras de respuesta HTTP).*  
> ![Captura 5.2 - Servidor de Aplicaciones en Operación](ruta/a/tu/captura_servidor_operacion.png)

---

### 5.3 Gestión de Entorno, Almacenamiento y Bitácoras del Servidor (`.env`, `storage/` y `config/logging.php`)
- **Configuración por variables de entorno (`.env`):** Centraliza la clave simétrica `APP_KEY`, credenciales de conexión a MariaDB (`DB_HOST=127.0.0.1`, `DB_PORT=3306`, `DB_DATABASE=proyectoalan`) y drivers de caché y sesión (`file`).
- **Almacenamiento público enlazado (`public/storage`):** Mediante `php artisan storage:link` se enlaza `storage/app/public` para servir de forma controlada las portadas de cursos (`storage/images/`).
- **Bitácoras y observabilidad (`config/logging.php`):** Separa los registros del servidor en canales dedicados con rotación diaria por 30 días: `storage/logs/laravel.log` (errores de aplicación), `storage/logs/auth.log` (eventos de inicio de sesión) y `storage/logs/security.log` (alertas de seguridad registradas por `RequestSecurityLogger`).
