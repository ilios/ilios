<?php

namespace AppBundle\Service;

use AppBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Encoder;

use AppBundle\Entity\UserInterface;
use Firebase\JWT\JWT;
use DateTime;
use DateInterval;

class JsonWebTokenManager
{
    const PREPEND_KEY = 'ilios.jwt.key.';
    const TOKEN_ISS = 'ilios';
    const TOKEN_AUD = 'ilios';
    
    /**
     * @var string
     */
    protected $jwtKey;

    /**
     * @var PermissionChecker
     */
    protected $permissionChecker;

    /**
     * @var SessionUserProvider
     */
    protected $sessionUserProvider;

    /**
     * Constructor
     * @param PermissionChecker $permissionChecker
     * @param SessionUserProvider $sessionUserProvider
     * @param string $secretKey injected kernel secret key
     */
    public function __construct(
        PermissionChecker $permissionChecker,
        SessionUserProvider $sessionUserProvider,
        $secretKey
    ) {
        $this->permissionChecker = $permissionChecker;
        $this->sessionUserProvider = $sessionUserProvider;
        $this->jwtKey = self::PREPEND_KEY . $secretKey;
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
        $datetime = new DateTime();
        $datetime->setTimestamp($arr['iat']);
        
        return $datetime;
    }
    
    public function getExpiresAtFromToken($jwt)
    {
        $arr = $this->decode($jwt);
        $datetime = new DateTime();
        $datetime->setTimestamp($arr['exp']);
        
        return $datetime;
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
        $decoded = JWT::decode($jwt, $this->jwtKey, array('HS256'));
        return (array) $decoded;
    }

    /**
     * Build a token from a user
     * @param  SessionUserInterface $sessionUser
     * @param string $timeToLive PHP DateInterval notation for the length of time the token shoud be valid
     * @return string
     * @throws \Exception
     */
    public function createJwtFromSessionUser(SessionUserInterface $sessionUser, $timeToLive = 'PT8H')
    {
        $requestedInterval = new \DateInterval($timeToLive);
        $maximumInterval = new \DateInterval('P364D');
        $now = new DateTime();

        //DateIntervals are not comparable so we have to create DateTimes first with are
        $requestedFromToday = clone $now;
        $requestedFromToday->add($requestedInterval);
        $maximumFromToday = clone $now;
        $maximumFromToday->add($maximumInterval);

        $interval = $requestedFromToday > $maximumFromToday?$maximumInterval:$requestedInterval;
        $expires = clone $now;
        $expires->add($interval);
        $canCreateOrUpdateUserInAnySchool = $this->permissionChecker->canCreateOrUpdateUsersInAnySchool($sessionUser);

        $arr = array(
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'user_id' => $sessionUser->getId(),
            'is_root' => $sessionUser->isRoot(),
            'performs_non_learner_function' => $sessionUser->performsNonLearnerFunction(),
            'can_create_or_update_user_in_any_school' => $canCreateOrUpdateUserInAnySchool,
        );

        return JWT::encode($arr, $this->jwtKey);
    }

    /**
     * Build a token from a user
     * @param  UserInterface $user
     * @param string $timeToLive PHP DateInterval notation for the length of time the token shoud be valid
     * @return string
     */
    public function createJwtFromUser(UserInterface $user, $timeToLive = 'PT8H')
    {
        return $this->createJwtFromUserId($user->getId(), $timeToLive);
    }

    /**
     * Build a token from a userId
     * @param  integer $userId
     * @param string $timeToLive PHP DateInterval notation for the length of time the token shoud be valid
     *
     * @return string
     */
    public function createJwtFromUserId($userId, $timeToLive = 'PT8H')
    {
        $sessionUser = $this->sessionUserProvider->createSessionUserFromUserId($userId);
        return $this->createJwtFromSessionUser($sessionUser, $timeToLive);
    }
}
