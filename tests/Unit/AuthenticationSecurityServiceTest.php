<?php

namespace Tests\Unit;

use App\Application\Shared\Services\AuthenticationSecurityService;
use Tests\TestCase;

class AuthenticationSecurityServiceTest extends TestCase
{
    public function test_validate_email_allows_plus_addressing_for_valid_emails(): void
    {
        $service = app(AuthenticationSecurityService::class);

        $result = $service->validateEmail('user+tag@example.com');

        $this->assertTrue($result['is_valid']);
        $this->assertSame([], $result['errors']);
    }

    public function test_validate_email_detects_disposable_domains_case_insensitively(): void
    {
        $service = app(AuthenticationSecurityService::class);

        $result = $service->validateEmail('User@Mailinator.com');

        $this->assertFalse($result['is_valid']);
        $this->assertContains('Disposable email addresses are not allowed', $result['errors']);
    }

    public function test_generate_secure_password_rejects_lengths_shorter_than_requirement_floor(): void
    {
        $service = app(AuthenticationSecurityService::class);

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Secure password length must be at least 4 characters.');

        $service->generateSecurePassword(3);
    }
}
