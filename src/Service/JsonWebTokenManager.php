<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUserInterface;
use DateTimeInterface;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTime;

use function array_key_exists;

class JsonWebTokenManager
{
    public const PREPEND_KEY = 'ilios.jwt.key.';
    private const TOKEN_ISS = 'ilios';
    private const TOKEN_AUD = 'ilios';
    public const SIGNING_ALGORITHM = 'HS256';

    protected string $jwtKey;

    public function __construct(
        protected PermissionChecker $permissionChecker,
        protected SessionUserProvider $sessionUserProvider,
        string $kernelSecret
    ) {
        $this->jwtKey = self::PREPEND_KEY . $kernelSecret;
        JWT::$leeway = 5;
    }

    public function getUserIdFromToken($jwt): int
    {
        $arr = $this->decode($jwt);
        return (int) $arr['user_id'];
    }

    public function getIssuedAtFromToken($jwt): DateTimeInterface
    {
        $arr = $this->decode($jwt);
        return DateTime::createFromFormat('U', (string) $arr['iat']);
    }

    public function getExpiresAtFromToken($jwt): DateTimeInterface
    {
        $arr = $this->decode($jwt);
        return DateTime::createFromFormat('U', (string) $arr['exp']);
    }

    public function getIsRootFromToken($jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['is_root'];
    }

    public function getPerformsNonLearnerFunctionFromToken($jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['performs_non_learner_function'];
    }

    public function getCanCreateOrUpdateUserInAnySchoolFromToken($jwt): bool
    {
        $arr = $this->decode($jwt);
        return $arr['can_create_or_update_user_in_any_school'];
    }

    public function getFirstCreatedAt($jwt): DateTimeInterface
    {
        $arr = $this->decode($jwt);
        if (array_key_exists('firstCreatedAt', $arr)) {
            $rhett = DateTime::createFromFormat('U', (string) $arr['firstCreatedAt']);
        } else {
            $rhett = new DateTime();
        }
        return $rhett;
    }

    public function getRefreshCount($jwt): int
    {
        $arr = $this->decode($jwt);
        return $arr['refreshCount'] ?? 0;
    }

    public function getPermissionsFromToken($jwt): string
    {
        $arr = $this->decode($jwt);
        return $arr['permissions'] ?? 'user';
    }

    protected function decode($jwt): array
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtKey, self::SIGNING_ALGORITHM));
        return (array) $decoded;
    }

    /**
     * Build a token from a user
     * @param string $timeToLive PHP DateInterval notation for the length of time the token should be valid
     */
    public function createJwtFromSessionUser(SessionUserInterface $sessionUser, string $timeToLive = 'PT8H'): string
    {
        $arr = $this->getTokenDetails($sessionUser, $timeToLive, null);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Refresh a token
     */
    public function refreshToken(string $token, string $timeToLive = 'PT8H'): string
    {
        $userId = $this->getUserIdFromToken($token);
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        $arr = $this->getTokenDetails($sessionUser, $timeToLive, $token);
        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    protected function getTokenDetails(
        SessionUserInterface $sessionUser,
        string $timeToLive,
        ?string $refreshToken
    ): array {
        $requestedInterval = new \DateInterval($timeToLive);
        $maximumInterval = new \DateInterval('P364D');
        $now = new DateTime();

        //DateIntervals are not comparable so we have to create DateTimes first which are
        $requestedFromToday = clone $now;
        $requestedFromToday->add($requestedInterval);
        $maximumFromToday = clone $now;
        $maximumFromToday->add($maximumInterval);

        $interval = $requestedFromToday > $maximumFromToday ? $maximumInterval : $requestedInterval;
        $expires = clone $now;
        $expires->add($interval);
        $canCreateOrUpdateUserInAnySchool = $this->permissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser);

        if ($refreshToken) {
            $firstCreatedAt = $this->getFirstCreatedAt($refreshToken);
            $refreshCount = $this->getRefreshCount($refreshToken) + 1;
        } else {
            $firstCreatedAt = clone $now;
            $refreshCount = 0;
        }

        return [
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' => $canCreateOrUpdateUserInAnySchool,
            'firstCreatedAt' => $firstCreatedAt->format('U'),
            'refreshCount' => $refreshCount,
            'permissions' => 'user', //all tokens are user tokens right now and get permissions from the user
        ];
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
}
