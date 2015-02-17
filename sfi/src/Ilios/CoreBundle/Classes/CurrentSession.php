<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\User;

class CurrentSession
{
    /**
     * @var User
     */
    protected $user;

    /**
     * Constructor
     * @param  User $user
     */
    public function __construct(User $user)
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
        return $this->user->getUserId();
    }
}
