<?php

namespace Database\Factories;

use App\Models\Tarjetas;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class TarjetasFactory extends Factory
{
    protected $model = Tarjetas::class;

    public function definition(): array
    {
        return [
            'usuario_id' => User::factory(),
            'numero_tarjeta' => $this->faker->creditCardNumber(),
            'expiracion' => $this->faker->regexify('(0[1-9]|1[0-2])/[0-9]{2}'),
            'cvv' => $this->faker->numerify('###'),
            'fecha_registro' => now(),
            'banco' => $this->faker->randomElement(['BVVA', 'BANCOAZTECA', 'BANAMEX', 'BANORTE']),
        ];
    }
}
