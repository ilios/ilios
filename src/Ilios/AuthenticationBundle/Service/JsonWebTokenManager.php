<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;
use Ilios\CoreBundle\Entity\UserInterface;

class JsonWebTokenManager
{
    /**
     * @var string
     */
    protected $secretKey;
    
    /**
     * Constructor
     * @param string $secretKey injected kernel secret key
     */
    public function __construct(
        $secretKey
    ) {
        $this->secretKey = $secretKey;
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
