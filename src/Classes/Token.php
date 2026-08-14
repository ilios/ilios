<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

/**
 * Base class for object representations of decoded JWT tokens.
 */
abstract readonly class Token
{
    public function __construct(
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public array $audience,
        public string $issuer,
    ) {
    }

    public function toArray(): array
    {
        return [
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
            'aud' => $this->audience,
            'iss' => $this->issuer,
        ];
    }
}
