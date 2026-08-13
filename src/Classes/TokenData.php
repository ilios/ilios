<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

abstract readonly class TokenData
{
    public function __construct(
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public array $audience,
    ) {
    }

    public function toArray(): array
    {
        return [
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
            'aud' => $this->audience,
        ];
    }
}
