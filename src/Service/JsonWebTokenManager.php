<?php

declare(strict_types=1);

namespace App\Service;

use App\Classes\SessionUserInterface;
use App\Entity\UserInterface;
use DateInterval;
use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use DateTime;

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

    public function getUserIdFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return $arr['user_id'];
    }

    public function getIssuedAtFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return DateTime::createFromFormat('U', (string) $arr['iat']);
    }

    public function getExpiresAtFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return DateTime::createFromFormat('U', (string) $arr['exp']);
    }

    public function getIsRootFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return $arr['is_root'];
    }

    public function getPerformsNonLearnerFunctionFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return $arr['performs_non_learner_function'];
    }

    public function getCanCreateOrUpdateUserInAnySchoolFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        return $arr['can_create_or_update_user_in_any_school'];
    }

    protected function decode($jwt)
    {
        $decoded = JWT::decode($jwt, new Key($this->jwtKey, self::SIGNING_ALGORITHM));
        return (array) $decoded;
    }

    /**
     * Build a token from a user
     */
    public function createJwtFromSessionUser(SessionUserInterface $sessionUser, string $timeToLive = 'PT8H'): string
    {
        $requestedInterval = new DateInterval($timeToLive);
        $maximumInterval = new DateInterval('P364D');
        $now = new DateTime();

        //DateIntervals are not comparable so we have to create DateTimes first with are
        $requestedFromToday = clone $now;
        $requestedFromToday->add($requestedInterval);
        $maximumFromToday = clone $now;
        $maximumFromToday->add($maximumInterval);

        $interval = $requestedFromToday > $maximumFromToday ? $maximumInterval : $requestedInterval;
        $expires = clone $now;
        $expires->add($interval);
        $canCreateOrUpdateUserInAnySchool = $this->permissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser);

        $arr = [
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' => $canCreateOrUpdateUserInAnySchool,
        ];

        return JWT::encode($arr, $this->jwtKey, self::SIGNING_ALGORITHM);
    }

    /**
     * Build a token from a user
     * @param string $timeToLive PHP DateInterval notation for the length of time the token shoud be valid
     */
    public function createJwtFromUser(UserInterface $user, $timeToLive = 'PT8H'): string
    {
        return $this->createJwtFromUserId($user->getId(), $timeToLive);
    }

    /**
     * Build a token from a userId
     * @param int $userId
     * @param string $timeToLive PHP DateInterval notation for the length of time the token shoud be valid
     */
    public function createJwtFromUserId($userId, $timeToLive = 'PT8H'): string
    {
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        return $this->createJwtFromSessionUser($sessionUser, $timeToLive);
    }
}
