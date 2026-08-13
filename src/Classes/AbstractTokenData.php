<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

abstract readonly class AbstractTokenData
{
    public const int DEFAULT_REFRESH_LIMIT = 12;
    public const string DEFAULT_PERMISSIONS = 'user';

    public function __construct(
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public string $permissions,
        public array $audience,
    ) {
    }

    public function toArray(): array
    {
        return [
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
           'permissions' => $this->permissions,
            'aud' => $this->audience,
        ];
    }
}
