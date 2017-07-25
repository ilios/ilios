<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\ApiBundle\Annotation as IS;

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
