<?php

namespace Ilios\AuthenticationBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Encoder;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

use Ilios\AuthenticationBundle\Jwt\Token as JwtToken;
use Ilios\CoreBundle\Entity\AuthenticationInterface as AuthenticationEntityInterface;
use Ilios\CoreBundle\Entity\UserInterface;

class JsonWebTokenManager
{
    /**
     * @var string
     */
    protected $secretKey;
    
    
    public function __construct(
        $secretKey
    ) {
        $this->secretKey = $secretKey;
    }
    
    public function buildToken(UserInterface $user)
    {
        $token = new JwtToken($this->secretKey);
        $token->setUser($user);
        
        return $token;
    }
}
