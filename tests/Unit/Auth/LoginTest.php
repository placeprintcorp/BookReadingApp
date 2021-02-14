<?php

namespace Tests\Unit\Auth;

use Tests\TestCase;

use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected function successful_login_route()
    {
        return route('home');
    }

    protected function login_get_route()
    {
        return route('login');
    }

    protected function login_post_route()
    {
        return route('login');
    }

    protected function logout_route()
    {
        return route('logout');
    }

    protected function successful_logout_route()
    {
        return '/';
    }

    protected function guest_middleware_route()
    {
        return route('home');
    }

    protected function get_too_many_login_attempts_message()
    {
        return sprintf('/^%s$/', str_replace('\:seconds', '\d+', preg_quote(__('auth.throttle'), '/')));
    }

    public function test_user_can_view_a_login_form()
    {
        $response = $this->get($this->login_get_route());

        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

    public function test_user_cannot_view_a_login_form_when_authenticated()
    {
        $user = User::factory()->make();

        $response = $this->actingAs($user)->get($this->login_get_route());

        $response->assertRedirect($this->guest_middleware_route());
    }

    public function test_user_can_login_with_correct_credentials()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = 'ken@18995'),
        ]);

        $response = $this->post($this->login_post_route(), [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertRedirect($this->successful_login_route());
        $this->assertAuthenticatedAs($user);
    }

    public function test_remember_me_functionality()
    {
        $user = User::factory()->create([
            'id' => random_int(1, 100),
            'password' => Hash::make($password = 'ken@18995'),
        ]);

        $response = $this->post($this->login_post_route(), [
            'email' => $user->email,
            'password' => $password,
            'remember' => 'on',
        ]);

        $user = $user->fresh();

        $response->assertRedirect($this->successful_login_route());
        $response->assertCookie(Auth::guard()->getRecallerName(), vsprintf('%s|%s|%s', [
            $user->id,
            $user->getRememberToken(),
            $user->password,
        ]));
        $this->assertAuthenticatedAs($user);
    }

    public function test_user_cannot_login_with_incorrect_password()
    {
        $user = User::factory()->create([
            'password' => Hash::make('ken@18995'),
        ]);

        $response = $this->from($this->login_get_route())->post($this->login_post_route(), [
            'email' => $user->email,
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->login_get_route());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_login_with_email_that_does_not_exist()
    {
        $response = $this->from($this->login_get_route())->post($this->login_post_route(), [
            'email' => 'nobody@mailinator.com',
            'password' => 'invalid-password',
        ]);

        $response->assertRedirect($this->login_get_route());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_can_logout()
    {
        $this->be(User::factory()->create());

        $response = $this->post($this->logout_route());

        $response->assertRedirect($this->successful_logout_route());
        $this->assertGuest();
    }

    public function test_user_cannot_logout_when_not_authenticated()
    {
        $response = $this->post($this->logout_route());

        $response->assertRedirect($this->successful_logout_route());
        $this->assertGuest();
    }

    public function test_user_cannot_make_more_than_five_attempts_in_one_minute()
    {
        $user = User::factory()->create([
            'password' => Hash::make($password = 'ken@18995'),
        ]);

        foreach (range(0, 5) as $_) {
            $response = $this->from($this->login_get_route())->post($this->login_post_route(), [
                'email' => $user->email,
                'password' => 'invalid-password',
            ]);
        }

        $response->assertRedirect($this->login_get_route());
        $response->assertSessionHasErrors('email');
        $this->assertMatchesRegularExpression(
            $this->get_too_many_login_attempts_message(),
            collect(
                $response
                    ->baseResponse
                    ->getSession()
                    ->get('errors')
                    ->getBag('default')
                    ->get('email')
            )->first()
        );
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}
