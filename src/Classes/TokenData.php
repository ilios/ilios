<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

/**
 * Immutable representation of a decoded JWT.
 */
readonly class TokenData
{
    public const int DEFAULT_REFRESH_LIMIT = 12;
    public const string DEFAULT_PERMISSIONS = 'user';

    public function __construct(
        public int $userId,
        public int $serviceTokenId,
        public bool $isUserToken,
        public bool $isServiceToken,
        public DateTimeImmutable $issuedAt,
        public DateTimeImmutable $expiresAt,
        public bool $isRoot,
        public bool $performsNonLearnerFunction,
        public bool $canCreateOrUpdateUserInAnySchool,
        public ?int $issuedWith,
        public DateTimeImmutable $firstCreatedAt,
        public int $refreshCount,
        public int $refreshLimit,
        public string $permissions,
        public array $writeableSchoolIds,
        public bool $canCreateUserTokensFromToken,
        public array $audience
    ) {
    }

    public function toArray(): array
    {
        $rhett = [
            'user_id' => $this->userId,
            'token_id' => $this->serviceTokenId,
            'iat' => $this->issuedAt->format('U'),
            'exp' => $this->expiresAt->format('U'),
            'is_root' => $this->isRoot,
            'performs_non_learner_function' => $this->performsNonLearnerFunction,
            'can_create_or_update_user_in_any_school' => $this->canCreateOrUpdateUserInAnySchool,
            'firstCreatedAt' => $this->firstCreatedAt->format('U'),
            'refreshCount' => $this->refreshCount,
            'refreshLimit' => $this->refreshLimit,
            'permissions' => $this->permissions,
            'writeable_schools' => $this->writeableSchoolIds,
            'can_generate_user_tokens' => $this->canCreateUserTokensFromToken,
            'aud' => $this->audience,
        ];
        if (!is_null($this->issuedWith)) {
            $rhett['issuedWith'] = $this->issuedWith;
        }

        return $rhett;
    }
}
