<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordUpdateTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_can_be_updated(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/perfil/edit')
            ->put('/password', [
                'current_password' => 'password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response
            ->assertSessionHasNoErrors()
            ->assertRedirect('/perfil/edit');

        $this->assertTrue(
            Hash::check('NewPassword123!', $user->refresh()->senha)
        );
    }

    public function test_correct_password_must_be_provided_to_update_password(): void
    {
        $user = User::factory()->create();

        $response = $this
            ->actingAs($user)
            ->from('/perfil/edit')
            ->put('/password', [
                'current_password' => 'wrong-password',
                'password' => 'NewPassword123!',
                'password_confirmation' => 'NewPassword123!',
            ]);

        $response
            ->assertSessionHasErrorsIn(
                'updatePassword',
                'current_password'
            )
            ->assertRedirect('/perfil/edit');

        $this->assertTrue(
            Hash::check('password', $user->refresh()->senha)
        );
    }
}
