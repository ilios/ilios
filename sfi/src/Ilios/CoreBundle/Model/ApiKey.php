<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Model\UserInterface;

/**
 * ApiKey
 */
class ApiKey implements ApiKeyInterface
{
    /**
     * @var string
     */
    private $key;

    /**
     * @var UserInterface
     */
    private $user;

    /**
     * @param string $key
     */
    public function setKey($key)
    {
        $this->key = $key;
    }

    /**
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user = null)
    {
        $this->user = $user;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }
}
