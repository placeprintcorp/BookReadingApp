<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ResetPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function get_valid_token($user)
    {
        return Password::broker()->createToken($user);
    }

    protected function get_invalid_token()
    {
        return 'invalid-token';
    }

    protected function password_reset_get_route($token)
    {
        return route('password.reset', $token);
    }

    protected function password_reset_post_route()
    {
        return '/password/reset';
    }

    protected function successful_password_reset_route()
    {
        return route('home');
    }

    public function testUserCanViewAPasswordResetForm()
    {
        $user = User::factory()->create();

        $response = $this->get($this->password_reset_get_route($token = $this->get_valid_token($user)));

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    public function testUserCanViewAPasswordResetFormWhenAuthenticated()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get($this->password_reset_get_route($token = $this->get_valid_token($user)));

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.reset');
        $response->assertViewHas('token', $token);
    }

    public function testUserCanResetPasswordWithValidToken()
    {
        Event::fake();
        $user = User::factory()->create();

        $response = $this->post($this->password_reset_post_route(), [
            'token' => $this->get_valid_token($user),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->successful_password_reset_route());
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('new-awesome-password', $user->fresh()->password));
        $this->assertAuthenticatedAs($user);
        Event::assertDispatched(PasswordReset::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }

    public function test_user_cannot_reset_password_with_invalid_token()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from($this->password_reset_get_route($this->get_invalid_token()))->post($this->password_reset_post_route(), [
            'token' => $this->get_invalid_token(),
            'email' => $user->email,
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->password_reset_get_route($this->get_invalid_token()));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function test_user_cannot_reset_password_without_providing_a_new_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from($this->password_reset_get_route($token = $this->get_valid_token($user)))->post($this->password_reset_post_route(), [
            'token' => $token,
            'email' => $user->email,
            'password' => '',
            'password_confirmation' => '',
        ]);

        $response->assertRedirect($this->password_reset_get_route($token));
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }

    public function test_user_cannot_reset_password_without_providing_an_email()
    {
        $user = User::factory()->create([
            'password' => Hash::make('old-password'),
        ]);

        $response = $this->from($this->password_reset_get_route($token = $this->get_valid_token($user)))->post($this->password_reset_post_route(), [
            'token' => $token,
            'email' => '',
            'password' => 'new-awesome-password',
            'password_confirmation' => 'new-awesome-password',
        ]);

        $response->assertRedirect($this->password_reset_get_route($token));
        $response->assertSessionHasErrors('email');
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertEquals($user->email, $user->fresh()->email);
        $this->assertTrue(Hash::check('old-password', $user->fresh()->password));
        $this->assertGuest();
    }
}
