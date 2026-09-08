<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminUserManagementTest extends TestCase
{
    use RefreshDatabase;

    private function admin(): User
    {
        return User::factory()->create(['is_admin' => true, 'is_active' => true]);
    }

    private function protectedAdmin(): User
    {
        return User::factory()->create([
            'email' => 'admin@chronos.br',
            'is_admin' => true,
            'is_active' => true,
        ]);
    }

    public function test_non_admin_cannot_access_users_index(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('admin.users.index'))
            ->assertForbidden();
    }

    public function test_admin_can_toggle_active_of_regular_user(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['is_active' => true]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $target))
            ->assertRedirect();

        $this->assertFalse($target->fresh()->is_active);
    }

    public function test_admin_can_toggle_admin_flag(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create(['is_admin' => false]);

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-admin', $target))
            ->assertRedirect();

        $this->assertTrue($target->fresh()->is_admin);
    }

    public function test_cannot_block_protected_admin(): void
    {
        $admin = $this->admin();
        $protected = $this->protectedAdmin();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $protected))
            ->assertForbidden();

        $this->assertTrue($protected->fresh()->is_active);
    }

    public function test_cannot_demote_protected_admin(): void
    {
        $admin = $this->admin();
        $protected = $this->protectedAdmin();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-admin', $protected))
            ->assertForbidden();
    }

    public function test_cannot_delete_protected_admin(): void
    {
        $admin = $this->admin();
        $protected = $this->protectedAdmin();

        $this->actingAs($admin)
            ->delete(route('admin.users.destroy', $protected))
            ->assertForbidden();

        $this->assertDatabaseHas('users', ['id' => $protected->id]);
    }

    public function test_admin_cannot_block_themselves(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-active', $admin))
            ->assertForbidden();
    }

    public function test_admin_cannot_demote_themselves(): void
    {
        $admin = $this->admin();

        $this->actingAs($admin)
            ->patch(route('admin.users.toggle-admin', $admin))
            ->assertForbidden();
    }

    public function test_blocked_user_cannot_login(): void
    {
        $user = User::factory()->create([
            'email' => 'blocked@example.com',
            'password' => bcrypt('password'),
            'is_active' => false,
        ]);

        $response = $this->post(route('login'), [
            'email' => 'blocked@example.com',
            'password' => 'password',
        ]);

        $response->assertSessionHasErrors('email');
        $this->assertGuest();
        $this->assertStringContainsString(
            'Sua conta está bloqueada.',
            (string) session('errors')->first('email')
        );
    }

    public function test_admin_can_update_user_name_and_email(): void
    {
        $admin = $this->admin();
        $target = User::factory()->create();

        $this->actingAs($admin)
            ->patch(route('admin.users.update', $target), [
                'name' => 'Novo Nome',
                'email' => 'novo@example.com',
            ])
            ->assertRedirect();

        $target->refresh();
        $this->assertSame('Novo Nome', $target->name);
        $this->assertSame('novo@example.com', $target->email);
    }
}
