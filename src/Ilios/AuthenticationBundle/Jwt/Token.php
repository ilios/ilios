<?php

namespace Ilios\AuthenticationBundle\Jwt;

use Symfony\Component\Security\Core\Authentication\Token\AbstractToken;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Role\Role;
use Symfony\Component\Security\Core\Role\RoleInterface;
use JWT as TokenLib;

use Ilios\CoreBundle\Entity\UserInterface;

class Token extends AbstractToken
{
    /**
     * The key we use to sign and validate
     * @var array
     */
    protected $key;

    /**
     * Our JWT token
     * @var array
     */
    protected $jwt;

    /**
     * @var UserInterface
     */
    protected $user;

    public function __construct($key)
    {
        //allow for 5 seconds of clock skew
        TokenLib::$leeway = 5;
        $this->key = $key;
    }

    public function setRequest(Request $request)
    {
        $jwt = false;

        $authorizationHeader = $request->headers->get('Authorization');
        $matches = [];
        // we always take the Authorization header over the query param
        if (preg_match('/^Token (.*)$/', $authorizationHeader, $matches)) {
            $jwt = $matches[1];
        }
        if ($jwt) {
            $decoded = TokenLib::decode($jwt, $this->key, array('HS256'));
            $this->jwt = (array) $decoded;
        }
    }

    public function setUser($user)
    {
        if (!$user instanceof UserInterface) {
            throw new InvalidArgumentException(
                'Set user only accepts User Entites ' .
                'argument was a ' . get_class($user)
            );
        }
        $this->roles = array();
        foreach ($user->getRoles() as $roleEntity) {
            $role = new Role($roleEntity->getRole());
            $this->roles[] = $role;
        }
        $this->user = $user;
        $this->setAuthenticated(true);
    }

    public function getUserName()
    {
        return $this->user->getEmail();
    }

    /**
     * Check if our JWT token was found in the request
     * @return boolean
     */
    public function isValidJwtRequest()
    {
        if (is_array($this->jwt)) {
            if (!$this->jwt['iss'] === 'ilios') {
                return false;
            }
            if (!array_key_exists('user_id', $this->jwt)) {
                return false;
            }
            return true;
        }

        return false;
    }

    public function getCredentials()
    {
        return (int) $this->jwt['user_id'];
    }

    /**
     * {@inheritdoc}
     */
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * {@inheritdoc}
     */
    public function serialize()
    {
        return serialize(
            $this->jwt
        );
    }
    /**
     * {@inheritdoc}
     */
    public function unserialize($serialized)
    {
        $this->jwt = unserialize($serialized);
        $this->setAuthenticated(false);
    }

    public function getJwt()
    {
        if (!$this->user) {
            throw new \Exception('Can not build a JWT, we have no user');
        }
        $now = new \DateTime();
        $arr = array(
            'iss' => 'ilios',
            'aud' => 'ilios',
            'iat' => $now->format('U'),
            'user_id' => $this->user->getId()
        );

        return TokenLib::encode($arr, $this->key);
    }
}
