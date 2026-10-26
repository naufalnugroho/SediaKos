<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Tests\TestCase;

class AdminAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_guest_is_redirected_from_admin_dashboard(): void
    {
        $this->get(route('admin.dashboard'))
            ->assertRedirect(route('login'));
    }

    public function test_login_page_is_publicly_available(): void
    {
        $this->get(route('login'))
            ->assertOk()
            ->assertSee('Masuk akun');
    }

    public function test_admin_can_login_and_open_dashboard(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-login-test-'.Str::uuid().'@example.com',
            'password' => Hash::make('password-rahasia'),
            'role' => 'admin',
        ]);

        $this->post(route('login.store'), [
            'email' => $admin->email,
            'password' => 'password-rahasia',
        ])->assertRedirect(route('admin.dashboard'));

        $this->assertAuthenticatedAs($admin);
        $this->get(route('admin.dashboard'))->assertOk();
    }

    public function test_invalid_password_is_rejected(): void
    {
        $admin = User::factory()->create([
            'email' => 'admin-invalid-login-test-'.Str::uuid().'@example.com',
            'password' => Hash::make('password-benar'),
            'role' => 'admin',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $admin->email,
                'password' => 'password-salah',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }

    public function test_non_admin_cannot_login_to_admin_area(): void
    {
        $user = User::factory()->create([
            'email' => 'staff-login-test-'.Str::uuid().'@example.com',
            'password' => Hash::make('password-staff'),
            'role' => 'staff',
        ]);

        $this->from(route('login'))
            ->post(route('login.store'), [
                'email' => $user->email,
                'password' => 'password-staff',
            ])
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors('email');

        $this->assertGuest();
    }
}
