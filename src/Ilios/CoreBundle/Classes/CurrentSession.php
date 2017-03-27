<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\UserInterface;

class CurrentSession
{
    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * Constructor
     * @param  User $user
     */
    public function __construct(UserInterface $user)
    {
        $this->user = $user;
    }

    /**
     * Get the user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->user->getId();
    }
}
