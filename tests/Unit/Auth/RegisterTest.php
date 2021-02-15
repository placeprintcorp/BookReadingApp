<?php

namespace Tests\Unit\Auth;


use App\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;


class RegisterTest extends TestCase
{
    use RefreshDatabase;

    protected function successful_registration_route()
    {
        return route('home');
    }

    protected function register_get_route()
    {
        return route('register');
    }

    protected function register_post_route()
    {
        return route('register');
    }

    protected function guestMiddlewareRoute()
    {
        return route('home');
    }

    public function testUserCanViewARegistrationForm()
    {
        $response = $this->get($this->register_get_route());

        $response->assertSuccessful();
        $response->assertViewIs('auth.register');
    }

    public function testUserCannotViewARegistrationFormWhenAuthenticated()
    {
        $user = factory(\App\User::class)->make();

        $response = $this->actingAs($user)->get($this->register_get_route());

        $response->assertRedirect($this->guestMiddlewareRoute());
    }

    public function testUserCanRegister()
    {
        Event::fake();

        $response = $this->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => 'kenpatel189@mailinator.com',
            'password' => 'ken@18995',
            'password_confirmation' => 'ken@18995',
        ]);

        $response->assertRedirect($this->successful_registration_route());
        $this->assertCount(1, $users = User::all());
        $this->assertAuthenticatedAs($user = $users->first());
        $this->assertEquals('Ken Patel', $user->name);
        $this->assertEquals('kenpatel189@mailinator.com', $user->email);
        $this->assertTrue(Hash::check('ken@18995', $user->password));
        Event::assertDispatched(Registered::class, function ($e) use ($user) {
            return $e->user->id === $user->id;
        });
    }

    public function test_user_cannot_register_without_name()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => '',
            'email' => 'kenpatel189@mailinator.com',
            'password' => 'ken@18995',
            'password_confirmation' => 'ken@18995',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('name');
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_register_without_email()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => '',
            'password' => 'ken@18995',
            'password_confirmation' => 'ken@18995',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_register_with_invalid_email()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => 'invalid-email',
            'password' => 'ken@18995',
            'password_confirmation' => 'ken@18995',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('email');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_register_without_password()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => 'kenpatel189@mailinator.com',
            'password' => '',
            'password_confirmation' => '',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_register_without_password_confirmation()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => 'kenpatel189@mailinator.com',
            'password' => 'ken@18995',
            'password_confirmation' => '',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }

    public function test_user_cannot_register_with_passwords_not_matching()
    {
        $response = $this->from($this->register_get_route())->post($this->register_post_route(), [
            'name' => 'Ken Patel',
            'email' => 'kenpatel189@mailinator.com',
            'password' => 'ken@18995',
            'password_confirmation' => 'i-love-symfony',
        ]);

        $users = User::all();

        $this->assertCount(0, $users);
        $response->assertRedirect($this->register_get_route());
        $response->assertSessionHasErrors('password');
        $this->assertTrue(session()->hasOldInput('name'));
        $this->assertTrue(session()->hasOldInput('email'));
        $this->assertFalse(session()->hasOldInput('password'));
        $this->assertGuest();
    }
}
