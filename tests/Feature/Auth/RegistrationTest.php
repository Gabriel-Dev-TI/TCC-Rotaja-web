<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use RefreshDatabase;

    public function test_registration_screen_can_be_rendered(): void
    {
        $this->get('/registro')->assertOk();
    }

    public function test_new_users_can_register(): void
    {
        $response = $this->post('/registro', [
            'nome' => 'Entregador Teste',
            'email' => 'entregador@example.com',
            'telefone' => '(37) 99999-9999',
            'tipo' => 'entregador',
            'cpf' => '123.456.789-00',
            'tipo_veiculo' => 'moto',
            'senha' => 'Password123!',
            'senha_confirmation' => 'Password123!',
        ]);

        $response->assertRedirect(route('entregador.dashboard'));

        $this->assertAuthenticated();

        $this->assertDatabaseHas('usuarios', [
            'nome' => 'Entregador Teste',
            'email' => 'entregador@example.com',
            'cargo' => 'entregador',
        ]);
    }
}
