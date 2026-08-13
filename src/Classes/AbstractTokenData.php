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
        public bool $isRoot,
        public bool $performsNonLearnerFunction,
        public ?int $issuedWith,
        public DateTimeImmutable $firstCreatedAt,
        public int $refreshCount,
        public int $refreshLimit,
        public string $permissions,
        public array $audience,
    ) {
    }

    public function toArray(): array
    {
        $rhett = [
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
            'is_root' => $this->isRoot,
            'performs_non_learner_function' => $this->performsNonLearnerFunction,
            'firstCreatedAt' => $this->firstCreatedAt->format('U'),
            'refreshCount' => $this->refreshCount,
            'refreshLimit' => $this->refreshLimit,
            'permissions' => $this->permissions,
            'aud' => $this->audience,
        ];
        if (!is_null($this->issuedWith)) {
            $rhett['issuedWith'] = $this->issuedWith;
        }
        return $rhett;
    }
}
