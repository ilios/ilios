<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceToken;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use DateInterval;
use DateTimeImmutable;

/**
 * Utilities for dealing with authentication tokens.
 */
readonly class TokenManager
{
    /**
     * The default time-to-live (TTL) for generated user tokens.
     */
    public const string USER_TOKEN_DEFAULT_TTL = 'PT8H';

    /**
     * The maximum time-to-live for any generated token.
     */
    public const string TOKEN_MAX_TTL = 'P90D';

    /**
     * The default token issuer claim.
     */
    public const string TOKEN_ISSUER = 'ilios';

    /**
     * The default token audience claim.
     */
    public const string TOKEN_AUDIENCE = 'ilios';

    public function __construct(
        protected TokenCodec $codec,
        protected TokenFactory $factory,
        protected SessionUserPermissionChecker $sessionUserPermissionChecker,
    ) {
    }

    /**
     * Extracts and returns data from a given JSON Web Token (JWT).
     * @param string $jwt The encoded token.
     * @return UserToken|ServiceToken The object representation of the given tokne.
     */
    public function extractJwt(string $jwt): UserToken|ServiceToken
    {
        $data = $this->codec->decode($jwt);
        return $this->factory->create($data);
    }

    /**
     * Creates a new user token for the given session user with a given time-to-live.
     */
    public function createUserTokenForSessionUser(
        SessionUserInterface $sessionUser,
        string $ttl = self::USER_TOKEN_DEFAULT_TTL
    ): UserToken {
        $issuedAt  = new DateTimeImmutable();
        $expiresAt = $this->getTokenExpirationDate($issuedAt, $ttl, self::TOKEN_MAX_TTL);
        $data = [
            'iat' => $issuedAt->format('U'),
            'exp' => $expiresAt->format('U'),
            'iss' => self::TOKEN_ISSUER,
            'aud' => self::TOKEN_AUDIENCE,
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' =>
                $this->sessionUserPermissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser),
            'firstCreatedAt' => $issuedAt->format('U'),
            'refreshCount' => 0,
        ];
        return $this->factory->create($data);
    }

    /**
     * Creates and returns a token expiration date.
     * The expiration date will be offset from the given issued-at date by the given time-to-live,
     * but not exceeding the given maximum time-to-live.
     */
    protected function getTokenExpirationDate(
        DateTimeImmutable $issuedAt,
        string $ttl,
        string $maxTtl
    ): DateTimeImmutable {
        $expirationDate = $issuedAt->add(new DateInterval($ttl));
        $maximumExpirationDate = $issuedAt->add(new DateInterval($maxTtl));
        return min($expirationDate, $maximumExpirationDate);
    }
}
