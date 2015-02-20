<?php

use Symfony\Component\Validator\Constraints as Assert;

namespace Ilios\CoreBundle\Entity;

/**
 * Interface ApiKeyInterface
 */
interface ApiKeyInterface
{
    /**
     * @param string $key
     */
    public function setKey($key);

    /**
     * @return string
     */
    public function getKey();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user = null);

    /**
     * @return UserInterface
     */
    public function getUser();
}
