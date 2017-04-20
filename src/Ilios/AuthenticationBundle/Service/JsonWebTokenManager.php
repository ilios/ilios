<?php

namespace Ilios\AuthenticationBundle\Service;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Symfony\Component\Security\Core\Encoder;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;
use Ilios\CoreBundle\Entity\UserInterface;
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
     * Constructor
     * @param string $secretKey injected kernel secret key
     */
    public function __construct(
        $secretKey
    ) {
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
     */
    public function createJwtFromSessionUser(SessionUserInterface $sessionUser, $timeToLive = 'PT8H')
    {
        return $this->createJwtFromUserId($sessionUser->getId(), $timeToLive);
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

        $arr = array(
            'iss' => self::TOKEN_ISS,
            'aud' => self::TOKEN_AUD,
            'iat' => $now->format('U'),
            'exp' => $expires->format('U'),
            'user_id' => $userId
        );

        return JWT::encode($arr, $this->jwtKey);
    }
}
