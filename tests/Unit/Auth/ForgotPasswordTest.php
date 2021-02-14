<?php

namespace Tests\Unit\Auth;

use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    protected function password_request_route()
    {
        return route('password.request');
    }

    protected function password_email_get_route()
    {
        return route('password.email');
    }

    protected function passwordEmailPostRoute()
    {
        return route('password.email');
    }

    public function testUserCanViewAnEmailPasswordForm()
    {
        $response = $this->get($this->password_request_route());

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    public function testUserCanViewAnEmailPasswordFormWhenAuthenticated()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get($this->password_request_route());

        $response->assertSuccessful();
        $response->assertViewIs('auth.passwords.email');
    }

    public function testUserReceivesAnEmailWithAPasswordResetLink()
    {
        Notification::fake();
        $user = User::factory()->create([
            'email' => 'john@example.com',
        ]);

        $response = $this->post($this->passwordEmailPostRoute(), [
            'email' => 'john@example.com',
        ]);

        $this->assertNotNull($token = DB::table('password_resets')->first());
        Notification::assertSentTo($user, ResetPassword::class, function ($notification, $channels) use ($token) {
            return Hash::check($notification->token, $token->token) === true;
        });
    }

    public function testUserDoesNotReceiveEmailWhenNotRegistered()
    {
        Notification::fake();

        $response = $this->from($this->password_email_get_route())->post($this->passwordEmailPostRoute(), [
            'email' => 'ken@mailinator.com',
        ]);

        $response->assertRedirect($this->password_email_get_route());
        $response->assertSessionHasErrors('email');
        Notification::assertNotSentTo(User::factory()->make(['email' => 'ken@mailinator.com']), ResetPassword::class);
    }

    public function test_email_is_required()
    {
        $response = $this->from($this->password_email_get_route())->post($this->passwordEmailPostRoute(), []);

        $response->assertRedirect($this->password_email_get_route());
        $response->assertSessionHasErrors('email');
    }

    public function test_email_is_a_valid_email()
    {
        $response = $this->from($this->password_email_get_route())->post($this->passwordEmailPostRoute(), [
            'email' => 'invalid-email',
        ]);

        $response->assertRedirect($this->password_email_get_route());
        $response->assertSessionHasErrors('email');
    }
}
