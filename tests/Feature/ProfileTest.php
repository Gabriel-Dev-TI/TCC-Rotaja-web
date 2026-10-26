<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_profile_page_is_displayed(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get('/perfil')
            ->assertOk();
    }

    public function test_profile_information_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'nome' => 'Usuario Teste',
                'email' => 'novo@example.com',
                'telefone' => '37999999999',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $user->refresh();

        $this->assertSame('Usuario Teste', $user->nome);
        $this->assertSame('novo@example.com', $user->email);
        $this->assertSame('37999999999', $user->telefone);
    }

    public function test_email_can_remain_unchanged_when_updating_profile(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->patch('/perfil', [
                'nome' => 'Usuario Teste',
                'email' => $user->email,
                'telefone' => $user->telefone,
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil');

        $this->assertSame(
            $user->email,
            $user->refresh()->email
        );
    }

    public function test_user_can_delete_their_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->delete('/perfil', [
                'password' => 'password',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/');

        $this->assertGuest();

        $this->assertSoftDeleted('usuarios', [
            'id' => $user->id,
        ]);
    }

    public function test_correct_password_must_be_provided_to_delete_account(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/perfil')
            ->delete('/perfil', [
                'password' => 'wrong-password',
            ]);

        $response
            ->assertSessionHasErrorsIn(
                'userDeletion',
                'password'
            )
            ->assertRedirect('/perfil');

        $this->assertDatabaseHas('usuarios', [
            'id' => $user->id,
            'deleted_at' => null,
        ]);
    }
}
