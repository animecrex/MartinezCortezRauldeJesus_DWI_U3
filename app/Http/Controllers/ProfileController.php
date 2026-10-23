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
            'expiracion' => ['required', 'regex:/^\d{2}\/\d{2}$/'],
            'cvv' => ['required', 'digits_between:3,4'],
            'banco' => ['required', 'in:BVVA,BANCOAZTECA,BANAMEX,BANORTE'],
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

        $card = [
            'id' => $tarjeta->id,
            'numero_tarjeta' => $tarjeta->numero_tarjeta,
            'expiracion' => $tarjeta->expiracion,
            'cvv' => $tarjeta->cvv,
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

        $validated = $request->validate([
            'numero_tarjeta' => ['required', 'digits_between:13,19'],
            'expiracion' => ['required', 'regex:/^\d{2}\/\d{2}$/'],
            'cvv' => ['required', 'digits_between:3,4'],
            'banco' => ['required', 'in:BVVA,BANCOAZTECA,BANAMEX,BANORTE'],
        ]);

        $tarjeta->update([
            'numero_tarjeta' => $validated['numero_tarjeta'],
            'expiracion' => $validated['expiracion'],
            'cvv' => $validated['cvv'],
            'banco' => $validated['banco'],
        ]);

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
