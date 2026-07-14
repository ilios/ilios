<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
use App\Entity\UserInterface;
use DateInterval;
use DateTimeImmutable;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Firebase\JWT\SignatureInvalidException;

use function array_key_exists;

class JsonWebTokenManager
{
    public const string PREPEND_KEY = 'ilios.jwt.key.';
    private const string TOKEN_ISS = 'ilios';
    private const string TOKEN_AUD = 'ilios';
    public const string SIGNING_ALGORITHM = 'HS256';

    public const string USER_TOKEN_DEFAULT_TTL = 'PT8H';

    public const string TOKEN_ID_KEY = 'token_id';
    public const string USER_ID_KEY = 'user_id';

    public const string ISSUED_WITH_KEY = 'issued_with';

    public const string WRITEABLE_SCHOOLS_KEY = 'writeable_schools';
    public const int DEFAULT_REFRESH_LIMIT = 12;
    public const string MAX_TIME_TO_LIVE = 'P90D';

    public const string CAN_GENERATE_USER_TOKENS_KEY = 'can_generate_user_tokens';

    public const string APPLICATION_SCOPE_KEY = 'application_scope';

    protected string $jwtKey;

    public function __construct(
        protected SessionUserPermissionChecker $permissionChecker,
        protected SessionUserProvider $sessionUserProvider,
        protected ServiceTokenUserProvider $serviceAccountUserProvider,
        protected SecretManager $secretManager,
    ) {
        $this->jwtKey = self::PREPEND_KEY . $this->secretManager->getSecret();
        JWT::$leeway = 5;
    }

    public function getUserIdFromToken(string $jwt): int
    {
        $arr = $this->decode($jwt);
        return (int) $arr[self::USER_ID_KEY];
    }

    public function getServiceTokenIdFromToken(string $jwt): int
    {
        $arr = $this->decode($jwt);
        return (int) $arr[self::TOKEN_ID_KEY];
    }

    public function isUserToken(string $jwt): bool
    {
        $arr = $this->decode($jwt);
        return array_key_exists(self::USER_ID_KEY, $arr);
    }

    public function isServiceToken(string $jwt): bool
    {
        $arr = $this->decode($jwt);
        return array_key_exists(self::TOKEN_ID_KEY, $arr);
    }

    public function getIssuedAtFromToken(string $jwt): DateTimeImmutable
    {
        $arr = $this->decode($jwt);
        return DateTimeImmutable::createFromFormat('U', (string) $arr['iat']);
    }

    public function getExpiresAtFromToken(string $jwt): DateTimeImmutable
    {
        $arr = $this->decode($jwt);
        return DateTimeImmutable::createFromFormat('U', (string) $arr['exp']);
    }

    public function getIsRootFromToken(string $jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['is_root'];
    }

    public function getPerformsNonLearnerFunctionFromToken(string $jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['performs_non_learner_function'];
    }

    public function getCanCreateOrUpdateUserInAnySchoolFromToken(string $jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['can_create_or_update_user_in_any_school'];
    }

    public function getIssuedWithFromToken(string $jwt): ?int
    {
        $arr = $this->decode($jwt);
        if (array_key_exists(self::ISSUED_WITH_KEY, $arr)) {
            return $arr[self::ISSUED_WITH_KEY];
        }
        return null;
    }

    public function getFirstCreatedAt(string $jwt): DateTimeImmutable
    {
        $arr = $this->decode($jwt);
        if (array_key_exists('firstCreatedAt', $arr)) {
            $rhett = DateTimeImmutable::createFromFormat('U', (string) $arr['firstCreatedAt']);
        } else {
            $rhett = $this->getIssuedAtFromToken($jwt);
        }
        return $rhett;
    }

    public function getRefreshCount(string $jwt): int
    {
        $arr = $this->decode($jwt);
        return $arr['refreshCount'] ?? 0;
    }

    public function getRefreshLimit(string $jwt): int
    {
        $arr = $this->decode($jwt);
        return $arr['refreshLimit'] ?? self::DEFAULT_REFRESH_LIMIT;
    }

    public function getPermissionsFromToken(string $jwt): string
    {
        $arr = $this->decode($jwt);
        return $arr['permissions'] ?? 'user';
    }

    public function getWriteableSchoolIdsFromToken(string $jwt): array
    {
        if (!$this->isServiceToken($jwt)) {
            return [];
        }
        $arr = $this->decode($jwt);
        if (!array_key_exists(self::WRITEABLE_SCHOOLS_KEY, $arr)) {
            return [];
        }
        if (!is_array($arr[self::WRITEABLE_SCHOOLS_KEY])) {
            return [];
        }
        return $arr[self::WRITEABLE_SCHOOLS_KEY];
    }

    public function getCanCreateUserTokensFromToken(string $jwt): bool
    {
        if (!$this->isServiceToken($jwt)) {
            return false;
        }
        $arr = $this->decode($jwt);
        if (!array_key_exists(self::CAN_GENERATE_USER_TOKENS_KEY, $arr)) {
            return false;
        }
        return $arr[self::CAN_GENERATE_USER_TOKENS_KEY];
    }

    public function getUserTokensApplicationScopeFromToken(string $jwt): string
    {
        $arr = $this->decode($jwt);
        if (!array_key_exists('aud', $arr)) {
            return '';
        }
        return $arr['aud'];
    }

    protected function decode(string $jwt): array
    {
        try {
            $decoded = JWT::decode($jwt, new Key($this->jwtKey, self::SIGNING_ALGORITHM));
            return (array) $decoded;
        } catch (SignatureInvalidException $e) {
            $transitionalSecret = $this->secretManager->getTransitionalSecret();
            if ($transitionalSecret) {
                $transitionalKey = self::PREPEND_KEY . $transitionalSecret;
                $decoded = JWT::decode($jwt, new Key($transitionalKey, self::SIGNING_ALGORITHM));
                return (array) $decoded;
            }
            throw $e;
        }
    }

    /**
     * Build a token from a user
     *
     * @param SessionUserInterface $sessionUser The current session user.
     * @param string $timeToLive PHP DateInterval notation for the length of time the token should be valid
     */
    public function createJwtFromSessionUser(
        SessionUserInterface $sessionUser,
        string $timeToLive = self::USER_TOKEN_DEFAULT_TTL,
    ): string {
        $arr = $this->getUserTokenDetails($sessionUser, $timeToLive);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Build a token from a service account token user
     */
    public function createJwtFromServiceTokenUser(
        ServiceTokenUserInterface $tokenUser,
        ?array $writeableSchoolIds = null,
        bool $canGenerateUserTokens = false,
        ?string $userTokensApplicationScope = '',
    ): string {
        $arr = $this->getServiceTokenDetails(
            $tokenUser,
            $writeableSchoolIds,
            $canGenerateUserTokens,
            $userTokensApplicationScope
        );
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Refresh a token
     */
    public function refreshToken(string $token, string $timeToLive = self::USER_TOKEN_DEFAULT_TTL): string
    {
        $refreshCount = $this->getRefreshCount($token);
        $refreshLimit = $this->getRefreshLimit($token);
        if ($refreshCount >= $refreshLimit) {
            throw new InvalidInputWithSafeUserMessageException("Refresh limit {$refreshLimit} exceeded");
        }

        $issuedAt = $this->getIssuedAtFromToken($token);
        $firstCreatedAt = $this->getFirstCreatedAt($token);
        $maximumInterval = new DateInterval(self::MAX_TIME_TO_LIVE);
        $maximumAge = new DateTimeImmutable()->sub($maximumInterval);
        if ($issuedAt <= $maximumAge || $firstCreatedAt <= $maximumAge) {
            throw new InvalidInputWithSafeUserMessageException("Token is too old to refresh");
        }

        $proposedExpiration = new DateTimeImmutable()->add(new DateInterval($timeToLive));
        $maximumExpiration = $firstCreatedAt->add(new DateInterval(self::MAX_TIME_TO_LIVE));

        if ($maximumExpiration < $proposedExpiration) {
            throw new InvalidInputWithSafeUserMessageException(
                "Invalid TTL value, maximum expiration date is \n{$maximumExpiration->format('c')}"
            );
        }

        $userId = $this->getUserIdFromToken($token);
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        $arr = $this->getUserTokenDetails($sessionUser, $timeToLive, refreshToken: $token);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Build a token from a userId
     *
     * @param string $timeToLive PHP DateInterval notation for the length of time the token should be valid
     */
    public function createJwtFromUserId(
        int $userId,
        string $timeToLive = self::USER_TOKEN_DEFAULT_TTL,
    ): string {
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        return $this->createJwtFromSessionUser($sessionUser, $timeToLive);
    }

    public function createJwtFromServiceTokenId(
        int $tokenId,
        ?array $writeableSchoolIds = [],
        bool $canCreateUserTokens = false,
        ?string $userTokensApplicationScope = ''
    ): string {
        $tokenUser = $this->serviceAccountUserProvider->createServiceTokenUserFromTokenId($tokenId);
        return $this->createJwtFromServiceTokenUser(
            $tokenUser,
            $writeableSchoolIds,
            $canCreateUserTokens,
            $userTokensApplicationScope
        );
    }

    /**
     * Creates a new user token for a given user with additional properties relayed
     * from the service token that's being used to create this user token.
     *
     * @param UserInterface $user The user that this token is created for.
     * @param int $serviceTokenId The ID of the service token that's used to create this user token.
     * @param string $applicationScope The application scope ("audience") of this user token.
     * @return string The user token as JWT.
     */
    public function createUserTokenFromServiceToken(
        UserInterface $user,
        int $serviceTokenId,
        string $applicationScope
    ): string {
        // collect the data needed to create a user token for the given user.
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($user->getId());
        $arr = $this->getUserTokenDetails($sessionUser, self::USER_TOKEN_DEFAULT_TTL, audience: $applicationScope);

        // bolt on the issued-with data point.
        $arr[self::ISSUED_WITH_KEY] = $serviceTokenId;
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    public function getUserTokenDetails(
        SessionUserInterface $sessionUser,
        string $timeToLive,
        string $audience = self::TOKEN_AUD,
        ?string $refreshToken = null,
    ): array {
        $now = new DateTimeImmutable();
        $expires = $this->getTokenExpirationDate($now, $timeToLive);
        $canCreateOrUpdateUserInAnySchool = $this->permissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser);
        $firstCreatedAt = $now;
        $refreshCount = 0;

        // If a refresh token was given, then some of its value should be "recycled"
        // by re-applying them to its replacement token that we're creating here.
        if ($refreshToken) {
            $firstCreatedAt = $this->getFirstCreatedAt($refreshToken);
            $refreshCount = $this->getRefreshCount($refreshToken) + 1;
            // ACHTUNG!
            // we're taking the 'audience' value straight from the given token instead of the given value.
            // in other words, one input arg can override another one in the process. no ideal.
            // TODO: clean this up [ST 2026/07/13]
            $audience = $this->getUserTokensApplicationScopeFromToken($refreshToken);
        }

        return [
            'iss' => self::TOKEN_ISS,
            'aud' => $audience,
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' => $canCreateOrUpdateUserInAnySchool,
            'firstCreatedAt' => $firstCreatedAt->format('U'),
            'refreshCount' => $refreshCount,
            'permissions' => 'user', //all tokens are user tokens right now and get permissions from the user
            self::USER_ID_KEY => $sessionUser->getId(),
        ];
    }

    protected function getServiceTokenDetails(
        ServiceTokenUserInterface $tokenUser,
        ?array $writeableSchoolIds = null,
        bool $canGenerateUserTokens = false,
        ?string $userTokensApplicationScope = '',
    ): array {
        $rhett = [
            'iss' => self::TOKEN_ISS,
            'aud' => $userTokensApplicationScope ?: self::TOKEN_AUD,
            'iat' => $tokenUser->getCreatedAt()->format('U'),
            'exp' => $tokenUser->getExpiresAt()->format('U'),
             self::TOKEN_ID_KEY => $tokenUser->getId(),
        ];
        if (is_array($writeableSchoolIds) && !empty($writeableSchoolIds)) {
            $rhett[self::WRITEABLE_SCHOOLS_KEY] = $writeableSchoolIds;
        }
        if ($canGenerateUserTokens) {
            $rhett[self::CAN_GENERATE_USER_TOKENS_KEY] = true;
        }
        return $rhett;
    }

    protected function getTokenExpirationDate(DateTimeImmutable $now, string $timeToLive): DateTimeImmutable
    {
        $requestedInterval = new DateInterval($timeToLive);
        $maximumInterval = new DateInterval(self::MAX_TIME_TO_LIVE);

        //DateIntervals are not comparable, so we have to create DateTimes first which are
        $requestedFromToday = $now->add($requestedInterval);
        $maximumFromToday = $now->add($maximumInterval);

        $interval = $requestedFromToday > $maximumFromToday ? $maximumInterval : $requestedInterval;

        return $now->add($interval);
    }
}
