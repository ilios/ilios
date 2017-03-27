<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CurrentSession
 * @package Ilios\CoreBundle\Classes
 *
 * @IS\DTO
 */
class CurrentSession
{
    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $userId;

    /**
     * Constructor
     * @param  UserInterface $user
     */
    public function __construct(UserInterface $user)
    {
        $this->userId = $user->getId();
    }

    /**
     * Get the user id
     *
     * @return int
     */
    public function getUserId()
    {
        return $this->userId;
    }
}
