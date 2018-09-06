<?php

namespace AppBundle\Classes;

use AppBundle\Classes\SessionUserInterface;
use AppBundle\Entity\UserInterface;
use AppBundle\Annotation as IS;

/**
 * Class CurrentSession
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
     * @param  SessionUserInterface $user
     */
    public function __construct(SessionUserInterface $user)
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
