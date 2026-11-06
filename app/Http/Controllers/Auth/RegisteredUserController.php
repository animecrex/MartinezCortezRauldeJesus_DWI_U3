<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => ['required', 'string', 'min:2', 'max:255', 'regex:/^[\pL\s\-]+$/u'],
            'usuario' => ['required', 'string', 'min:3', 'max:20', 'unique:users,usuario', 'regex:/^[a-zA-Z0-9_.-]+$/'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ], [
            'name.regex' => 'El nombre completo contiene caracteres no permitidos.',
            'usuario.unique' => 'Este nombre de usuario ya está en uso. Por favor elige otro.',
            'usuario.regex' => 'El usuario solo puede incluir letras, números, guiones y puntos.',
            'usuario.min' => 'El usuario debe tener al menos 3 caracteres.',
            'email.unique' => 'Este correo electrónico ya se encuentra registrado.',
            'password.confirmed' => 'La confirmación de la contraseña no coincide.',
        ]);

        $user = User::create([
            'name' => trim(strip_tags($request->name)),
            'usuario' => trim($request->usuario),
            'email' => strtolower(trim($request->email)),
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Log::channel('auth')->info(
            '[REGISTRO_USUARIO_NUEVO][IP: ' . $request->ip() . '][ID:' . $user->id . '][Usuario:' . $user->usuario . ']'
        );

        return response()->json([
            'message' => 'Usuario registrado correctamente'
        ]);
    }
}
