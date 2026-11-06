<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    public function create(): View
    {
        return view('auth.login');
    }

    public function store(Request $request): RedirectResponse
    {
        // 1. Validación de entradas
        $request->validate([
            'usuario' => ['required', 'string', 'max:50'],
            'password' => ['required', 'string'],
        ], [
            'usuario.required' => 'El nombre de usuario es obligatorio.',
            'password.required' => 'La contraseña es obligatoria.',
        ]);

        $usuario = trim($request->input('usuario'));
        $password = trim($request->input('password'));

        // 2. Clave para limitador de tasa (Brute Force Protection)
        $throttleKey = Str::lower($usuario) . '|' . $request->ip();

        // 3. Verificar si el usuario ha excedido el límite de intentos (5 intentos por minuto)
        if (RateLimiter::tooManyAttempts($throttleKey, 5)) {
            $seconds = RateLimiter::availableIn($throttleKey);

            Log::channel('auth')->warning(
                '[BLOQUEO_BRUTE_FORCE][IP: ' . $request->ip() . '][Usuario: ' . $usuario . '][Bloqueado por: ' . $seconds . 's]'
            );

            throw ValidationException::withMessages([
                'erroLogin' => 'Demasiados intentos fallidos. Por seguridad, espera ' . ceil($seconds / 60) . ' minuto(s) antes de intentar de nuevo.',
            ])->redirectTo('/login');
        }

        // 4. Intento seguro de autenticación (tiempo constante para mitigar timing attacks)
        $remember = $request->boolean('remember');

        if (Auth::attempt(['usuario' => $usuario, 'password' => $password], $remember)) {
            // Limpiar intentos fallidos al tener éxito
            RateLimiter::clear($throttleKey);

            // PREVENCIÓN DE SESSION FIXATION: regenerar ID de sesión
            $request->session()->regenerate();

            Log::channel('auth')->info(
                '[LOGIN_EXITOSO][IP: ' . $request->ip() . '][ID:' . Auth::id() . '][Usuario:' . $usuario . ']'
            );

            return redirect()->intended('/curso');
        }

        // 5. Registrar fallo e incrementar contador de intentos fallidos
        RateLimiter::hit($throttleKey, 60);

        Log::channel('auth')->warning(
            '[LOGIN_FALLIDO][IP: ' . $request->ip() . '][Usuario: ' . $usuario . '][Intentos restantes: ' . RateLimiter::remaining($throttleKey, 5) . ']'
        );

        throw ValidationException::withMessages([
            'erroLogin' => 'Usuario o contraseña incorrectos.',
        ])->redirectTo('/login');
    }

    public function destroy(Request $request): RedirectResponse
    {
        $userId = Auth::id();

        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        Log::channel('auth')->info(
            '[LOGOUT][IP: ' . $request->ip() . '][ID:' . ($userId ?? 'desconocido') . ']'
        );

        return redirect('/');
    }
}
