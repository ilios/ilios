<?php

declare(strict_types=1);

namespace App\Classes;

use DateTimeImmutable;

/**
 * An immutable object representation of a decoded JWT service token.
 */
readonly class ServiceToken extends Token
{
    public function __construct(
        DateTimeImmutable $issuedAt,
        DateTimeImmutable $expiresAt,
        array $audience,
        string $issuer,
        public int $serviceTokenId,
        public array $writeableSchoolIds,
        public bool $canCreateOrUpdateUserInAnySchool,
        public bool $canCreateUserTokensFromToken,
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
        return array_merge(
            parent::toArray(),
            [
                'token_id' => $this->serviceTokenId,
                'can_create_or_update_user_in_any_school' => $this->canCreateOrUpdateUserInAnySchool,
                'writeable_schools' => $this->writeableSchoolIds,
                'can_generate_user_tokens' => $this->canCreateUserTokensFromToken,
            ],
        );
    }
}
