<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

/**
 * Immutable representation of a decoded JWT.
 */
readonly class TokenData extends AbstractTokenData
{
    public function __construct(
        DateTimeImmutable $issuedAt,
        DateTimeImmutable $expiresAt,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        ?int $issuedWith,
        DateTimeImmutable $firstCreatedAt,
        int $refreshCount,
        int $refreshLimit,
        string $permissions,
        array $audience,
        public int $userId,
        public int $serviceTokenId,
        public bool $isUserToken,
        public bool $isServiceToken,
        public array $writeableSchoolIds,
        public bool $canCreateOrUpdateUserInAnySchool,
        public bool $canCreateUserTokensFromToken,
    ) {
        parent::__construct(
            $issuedAt,
            $expiresAt,
            $isRoot,
            $performsNonLearnerFunction,
            $issuedWith,
            $firstCreatedAt,
            $refreshCount,
            $refreshLimit,
            $permissions,
            $audience
        );
    }

    public function toArray(): array
    {
        return array_merge(
            parent::toArray(),
            [
                'user_id' => $this->userId,
                'token_id' => $this->serviceTokenId,
                'can_create_or_update_user_in_any_school' => $this->canCreateOrUpdateUserInAnySchool,
                'writeable_schools' => $this->writeableSchoolIds,
                'can_generate_user_tokens' => $this->canCreateUserTokensFromToken,
            ],
        );
    }
}
