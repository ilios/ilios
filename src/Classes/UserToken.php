<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

/**
 * An immutable object representation of a decoded JWT user token.
 */
readonly class UserToken extends Token
{
    public function __construct(
        DateTimeImmutable $issuedAt,
        DateTimeImmutable $expiresAt,
        array $audience,
        string $issuer,
        public int $userId,
        public bool $isRoot,
        public bool $performsNonLearnerFunction,
        public ?int $issuedWith,
        public DateTimeImmutable $firstCreatedAt,
        public int $refreshCount,
        public int $refreshLimit,
    ) {
        parent::__construct(
            $issuedAt,
            $expiresAt,
            $audience,
            $issuer
        );
    }

    public function toArray(): array
    {
        $rhett = array_merge(
            parent::toArray(),
            [
                'user_id' => $this->userId,
                'is_root' => $this->isRoot,
                'performs_non_learner_function' => $this->performsNonLearnerFunction,
                'firstCreatedAt' => $this->firstCreatedAt->format('U'),
                'refreshCount' => $this->refreshCount,
                'refreshLimit' => $this->refreshLimit,
            ],
        );
        if (!is_null($this->issuedWith)) {
            $rhett['issued_with'] = $this->issuedWith;
        }
        return $rhett;
    }
}
