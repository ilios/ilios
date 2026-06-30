<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Exception\InvalidInputWithSafeUserMessageException;
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

    public const string TOKEN_ID_KEY = 'token_id';
    public const string USER_ID_KEY = 'user_id';
    public const string WRITEABLE_SCHOOLS_KEY = 'writeable_schools';
    public const int DEFAULT_REFRESH_LIMIT = 12;
    public const string MAX_TIME_TO_LIVE = 'P90D';

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

    public function getFirstCreatedAt(string $jwt): DateTimeImmutable
    {
        $arr = $this->decode($jwt);
        if (array_key_exists('firstCreatedAt', $arr)) {
            $rhett = DateTimeImmutable::createFromFormat('U', (string) $arr['firstCreatedAt']);
        } else {
            $rhett = new DateTimeImmutable();
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
     * @param string $timeToLive PHP DateInterval notation for the length of time the token should be valid
     */
    public function createJwtFromSessionUser(SessionUserInterface $sessionUser, string $timeToLive = 'PT8H'): string
    {
        $arr = $this->getUserTokenDetails($sessionUser, $timeToLive, null);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Build a token from a service account token user
     */
    public function createJwtFromServiceTokenUser(
        ServiceTokenUserInterface $tokenUser,
        ?array $writeableSchoolIds = null,
    ): string {
        $arr = $this->getServiceTokenDetails($tokenUser, $writeableSchoolIds);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Refresh a token
     */
    public function refreshToken(string $token, string $timeToLive = 'PT8H'): string
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

        $userId = $this->getUserIdFromToken($token);
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        $arr = $this->getUserTokenDetails($sessionUser, $timeToLive, $token);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Build a token from a userId
     * @param string $timeToLive PHP DateInterval notation for the length of time the token should be valid
     */
    public function createJwtFromUserId(int $userId, string $timeToLive = 'PT8H'): string
    {
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        return $this->createJwtFromSessionUser($sessionUser, $timeToLive);
    }

    public function createJwtFromServiceTokenId(int $tokenId, ?array $writeableSchoolIds = []): string
    {
        $tokenUser = $this->serviceAccountUserProvider->createServiceTokenUserFromTokenId($tokenId);
        return $this->createJwtFromServiceTokenUser($tokenUser, $writeableSchoolIds);
    }

    protected function getUserTokenDetails(
        SessionUserInterface $sessionUser,
        string $timeToLive,
        ?string $refreshToken
    ): array {
        $now = new DateTimeImmutable();
        $expires = $this->getTokenExpirationDate($now, $timeToLive);
        $canCreateOrUpdateUserInAnySchool = $this->permissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser);

        if ($refreshToken) {
            $firstCreatedAt = $this->getFirstCreatedAt($refreshToken);
            $refreshCount = $this->getRefreshCount($refreshToken) + 1;
        } else {
            $firstCreatedAt = $now;
            $refreshCount = 0;
        }

        return [
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
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
    ): array {
        $rhett = [
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
            'iat' => $tokenUser->getCreatedAt()->format('U'),
            'exp' => $tokenUser->getExpiresAt()->format('U'),
             self::TOKEN_ID_KEY => $tokenUser->getId(),
        ];
        if (is_array($writeableSchoolIds) && !empty($writeableSchoolIds)) {
            $rhett[self::WRITEABLE_SCHOOLS_KEY] = $writeableSchoolIds;
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
