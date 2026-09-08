<?php

namespace Tests\Unit;

use App\Models\User;
use Tests\TestCase;

class UserAdminTest extends TestCase
{
    public function test_protected_admin_is_detected_case_insensitively(): void
    {
        config()->set('chronos.admin_emails', [
            'carloafernandes@gmail.com',
            'admin@chronos.br',
        ]);

        $user = new User(['email' => 'ADMIN@CHRONOS.BR']);

        $this->assertTrue($user->isProtectedAdmin());
        $this->assertTrue($user->isAdmin());
    }

    public function test_non_protected_email_is_not_protected_admin(): void
    {
        config()->set('chronos.admin_emails', ['admin@chronos.br']);

        $user = new User(['email' => 'joao@example.com']);

        $this->assertFalse($user->isProtectedAdmin());
    }

    public function test_regular_user_with_is_admin_true_is_admin(): void
    {
        config()->set('chronos.admin_emails', []);

        $user = new User(['email' => 'joao@example.com']);
        $user->is_admin = true;

        $this->assertTrue($user->isAdmin());
        $this->assertFalse($user->isProtectedAdmin());
    }
}
