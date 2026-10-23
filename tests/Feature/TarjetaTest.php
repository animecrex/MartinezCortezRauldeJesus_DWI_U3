<?php

namespace Tests\Feature;

use App\Models\Tarjetas;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TarjetaTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_register_a_card(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/registrar-tarjeta', [
            'numero_tarjeta' => '4111111111111111',
            'expiracion' => '12/30',
            'cvv' => '123',
            'banco' => 'BANAMEX',
        ]);

        $response->assertRedirect(route('vistaperfil'));
        $this->assertDatabaseHas('tarjetasbb', [
            'usuario_id' => $user->id,
            'numero_tarjeta' => '4111111111111111',
            'banco' => 'BANAMEX',
        ]);
    }

    public function test_user_can_update_a_saved_card(): void
    {
        $user = User::factory()->create();
        $card = Tarjetas::factory()->create([
            'usuario_id' => $user->id,
            'numero_tarjeta' => '4111111111111111',
            'expiracion' => '12/30',
            'cvv' => '123',
            'banco' => 'BANAMEX',
        ]);

        $response = $this->actingAs($user)->put('/tarjeta/' . $card->id, [
            'numero_tarjeta' => '5555555555554444',
            'expiracion' => '11/29',
            'cvv' => '321',
            'banco' => 'BANORTE',
        ]);

        $response->assertRedirect(route('vistaperfil'));
        $this->assertDatabaseHas('tarjetasbb', [
            'id' => $card->id,
            'numero_tarjeta' => '5555555555554444',
            'expiracion' => '11/29',
            'cvv' => '321',
            'banco' => 'BANORTE',
        ]);
    }
}
