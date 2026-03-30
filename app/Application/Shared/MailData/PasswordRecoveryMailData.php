<?php

namespace App\Application\Shared\MailData;

class PasswordRecoveryMailData
{
    public function __construct(
        public readonly string $title,
        public readonly string $email,
        public readonly string $resetUrl,
        public readonly string $shopName = 'Laravel Shop'
    ) {
    }
}
