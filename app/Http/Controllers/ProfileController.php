<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use App\Models\Tarjetas;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function vistaperfil()
    {
        $cards = Tarjetas::where('usuario_id', Auth::id())
            ->orderByDesc('fecha_registro')
            ->get()
            ->map(function ($card) {
                return [
                    'id' => $card->id,
                    'holder' => 'Titular',
                    'last4' => substr($card->numero_tarjeta, -4),
                    'expiry' => $card->expiracion,
                    'type' => $card->banco,
                ];
            });

        return view('tarjetas.perfil', compact('cards'));
    }

    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    public function registrartarjetas(Request $request): RedirectResponse
    {
        $numeroTarjeta = preg_replace('/[\s-]+/', '', (string) $request->input('numero_tarjeta'));
        $request->merge(['numero_tarjeta' => $numeroTarjeta]);

        $validated = $request->validate([
            'numero_tarjeta' => ['required', 'digits_between:13,19'],
            'expiracion' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'cvv' => ['required', 'digits_between:3,4'],
            'banco' => ['required', 'in:BVVA,BANCOAZTECA,BANAMEX,BANORTE'],
        ], [
            'numero_tarjeta.digits_between' => 'El número de tarjeta debe tener entre 13 y 19 dígitos.',
            'expiracion.regex' => 'El formato de expiración debe ser MM/AA válido.',
            'cvv.digits_between' => 'El código de seguridad CVV debe tener 3 o 4 dígitos.',
            'banco.in' => 'El banco seleccionado no es válido.',
        ]);

        Tarjetas::create([
            'usuario_id' => Auth::id(),
            'numero_tarjeta' => $validated['numero_tarjeta'],
            'expiracion' => $validated['expiracion'],
            'cvv' => $validated['cvv'],
            'fecha_registro' => now(),
            'banco' => $validated['banco'],
        ]);

        return Redirect::route('vistaperfil')->with('success', 'Tarjeta registrada correctamente.');
    }

    public function editarTarjeta(Tarjetas $tarjeta)
    {
        if ($tarjeta->usuario_id !== Auth::id()) {
            abort(403);
        }

        // Por seguridad el CVV real no se expone a través de la API
        $card = [
            'id' => $tarjeta->id,
            'numero_tarjeta' => $tarjeta->numero_tarjeta,
            'expiracion' => $tarjeta->expiracion,
            'cvv' => '***',
            'banco' => $tarjeta->banco,
        ];

        return response()->json($card);
    }

    public function actualizarTarjeta(Request $request, Tarjetas $tarjeta): RedirectResponse
    {
        if ($tarjeta->usuario_id !== Auth::id()) {
            abort(403);
        }

        $numeroTarjeta = preg_replace('/[\s-]+/', '', (string) $request->input('numero_tarjeta'));
        $request->merge(['numero_tarjeta' => $numeroTarjeta]);

        $rules = [
            'numero_tarjeta' => ['required', 'digits_between:13,19'],
            'expiracion' => ['required', 'regex:/^(0[1-9]|1[0-2])\/\d{2}$/'],
            'banco' => ['required', 'in:BVVA,BANCOAZTECA,BANAMEX,BANORTE'],
        ];

        // Validar CVV únicamente si el usuario lo modificó
        if ($request->filled('cvv') && $request->input('cvv') !== '***') {
            $rules['cvv'] = ['required', 'digits_between:3,4'];
        }

        $validated = $request->validate($rules, [
            'numero_tarjeta.digits_between' => 'El número de tarjeta debe tener entre 13 y 19 dígitos.',
            'expiracion.regex' => 'El formato de expiración debe ser MM/AA válido.',
            'cvv.digits_between' => 'El CVV debe tener 3 o 4 dígitos.',
        ]);

        $data = [
            'numero_tarjeta' => $validated['numero_tarjeta'],
            'expiracion' => $validated['expiracion'],
            'banco' => $validated['banco'],
        ];

        if (isset($validated['cvv'])) {
            $data['cvv'] = $validated['cvv'];
        }

        $tarjeta->update($data);

        return Redirect::route('vistaperfil')->with('success', 'Tarjeta actualizada correctamente.');
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
