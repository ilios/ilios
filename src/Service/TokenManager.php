<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceToken;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use App\Exception\InvalidInputWithSafeUserMessageException;
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
     * The TTL for short-lived user tokens.
     */
    public const string USER_TOKEN_SHORT_TTL = 'PT30S';

    /**
     * The maximum TTL for generated service tokens.
     */
    public const string SERVICE_TOKEN_MAX_TTL = 'P180D';

    /**
     * The maximum TTL for generated user token.
     */
    public const string USER_TOKEN_MAX_TTL = 'P90D';

    /**
     * The limit on how many times a user token can be refreshed.
     */
    public const int USER_TOKEN_REFRESH_LIMIT = 12;

    /**
     * The default token issuer claim.
     */
    public const string TOKEN_ISSUER = 'ilios';

    /**
     * The default token audience claim.
     */
    public const string TOKEN_AUDIENCE = 'ilios';

    /**
     * Audience claim for our Dashboard LTI frontend.
     */
    public const string TOKEN_LTI_DASHBOARD_AUDIENCE = 'lti-dashboard';


    public function __construct(
        protected TokenCodec $codec,
        protected TokenFactory $factory,
        protected SessionUserPermissionChecker $sessionUserPermissionChecker,
    ) {
    }

    /**
     * Extracts and returns data from a given JSON Web Token (JWT).
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
        $expiresAt = $this->getTokenExpirationDate($issuedAt, $ttl, self::USER_TOKEN_MAX_TTL);
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
     * Creates a fresh replacement for the given user and token, with a given time-to-live.
     */
    public function refreshUserToken(
        SessionUserInterface $sessionUser,
        UserToken $token,
        string $ttl = self::USER_TOKEN_DEFAULT_TTL
    ): UserToken {
        if ($token->refreshCount >= self::USER_TOKEN_REFRESH_LIMIT) {
            throw new InvalidInputWithSafeUserMessageException(
                sprintf('Refresh limit %s exceeded', self::USER_TOKEN_REFRESH_LIMIT)
            );
        }

        $maximumAge = new DateTimeImmutable()->sub(new DateInterval(self::USER_TOKEN_MAX_TTL));
        if ($token->issuedAt <= $maximumAge || $token->firstCreatedAt <= $maximumAge) {
            throw new InvalidInputWithSafeUserMessageException("Token is too old to refresh");
        }

        $iat = new DateTimeImmutable();
        $exp = $iat->add(new DateInterval($ttl));
        $maximumExp = $token->firstCreatedAt->add(new DateInterval(self::USER_TOKEN_MAX_TTL));
        if ($maximumExp < $exp) {
            throw new InvalidInputWithSafeUserMessageException(
                "Invalid TTL value, maximum expiration date is \n{$maximumExp->format('c')}"
            );
        }

        $data = [
            'iat' => $iat->format('U'),
            'exp' => $exp->format('U'),
            'iss' => $token->issuer,
            'aud' => $token->audience,
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' =>
                $this->sessionUserPermissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser),
            'firstCreatedAt' => $token->firstCreatedAt->format('U'),
            'refreshCount' => $token->refreshCount + 1,
        ];
        return $this->factory->create($data);
    }

    /**
     * Creates a new user token for the given user with a given service token.
     */
    public function createUserTokenFromServiceToken(
        SessionUserInterface $sessionUser,
        ServiceToken $serviceToken,
    ): UserToken {
        $iat = new DateTimeImmutable();
        $exp = $iat->add(new DateInterval(self::USER_TOKEN_SHORT_TTL));

        $data = [
            'iat' => $iat->format('U'),
            'exp' => $exp->format('U'),
            'iss' => self::TOKEN_ISSUER,
            'aud' => $serviceToken->audience,
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' =>
                $this->sessionUserPermissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser),
            'firstCreatedAt' => $iat->format('U'),
            'refreshCount' => 0,
            'issued_with' => $serviceToken->serviceTokenId,
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
