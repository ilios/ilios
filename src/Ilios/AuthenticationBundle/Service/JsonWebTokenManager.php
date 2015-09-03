<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\Security\Core\Encoder;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;
use Ilios\CoreBundle\Entity\UserInterface;
use JWT;
use DateTime;

class JsonWebTokenManager
{
    const PREPEND_KEY = 'ilios.jwt.key.';
    
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
        $datetime->setTimeStamp($arr['iat']);
        
        return $datetime;
    }
    
    protected function decode($jwt)
    {
        $decoded = JWT::decode($jwt, $this->jwtKey, array('HS256'));
        return (array) $decoded;
    }
    
    /**
     * Build a token from a user
     * @param  UserInterface $user
     * @return JwtToken
     */
    public function buildToken(UserInterface $user)
    {
        $token = new JwtToken($this->secretKey);
        $token->setUser($user);
        
        return $token;
    }
}
