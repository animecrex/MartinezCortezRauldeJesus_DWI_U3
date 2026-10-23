<?php

namespace Tests\Feature;

use App\Models\Curso;
use App\Models\Cursoreg;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CursoregControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_subscribe_to_a_course(): void
    {
        $user = User::factory()->create();
        $curso = Curso::create([
            'nombre' => 'Laravel',
            'descripcion' => 'Curso de prueba',
            'cant_alumnos' => 10,
            'maestro' => 'Maestro',
            'horas' => 20,
            'imagen' => 'curso.jpg',
            'costo' => 100,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-02-01',
            'objetivos' => 'Aprender',
            'requisitos' => 'Ninguno',
            'user_id' => $user->id,
        ]);

        $this->actingAs($user)
            ->postJson('/suscribirse', ['id_curso' => $curso->id])
            ->assertJsonPath('success', true);

        $this->assertDatabaseHas('cursos_reg', ['id_curso' => $curso->id, 'id_user' => $user->id]);
    }

    public function test_user_can_unsubscribe_from_a_course(): void
    {
        $user = User::factory()->create();
        $curso = Curso::create([
            'nombre' => 'Laravel',
            'descripcion' => 'Curso de prueba',
            'cant_alumnos' => 10,
            'maestro' => 'Maestro',
            'horas' => 20,
            'imagen' => 'curso.jpg',
            'costo' => 100,
            'fecha_inicio' => '2026-01-01',
            'fecha_fin' => '2026-02-01',
            'objetivos' => 'Aprender',
            'requisitos' => 'Ninguno',
            'user_id' => $user->id,
        ]);

        Cursoreg::create(['id_curso' => $curso->id, 'id_user' => $user->id]);

        $this->actingAs($user)
            ->postJson('/desuscribirse', ['id_curso' => $curso->id])
            ->assertJsonPath('success', true);

        $this->assertDatabaseMissing('cursos_reg', ['id_curso' => $curso->id, 'id_user' => $user->id]);
    }
}
