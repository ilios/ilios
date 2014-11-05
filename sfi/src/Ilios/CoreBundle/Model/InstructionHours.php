<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * InstructionHours
 */
class InstructionHours implements InstructionHoursInterface
{
    use IdentifiableEntity;
    use TimestampableEntity;

    /**
     * @var int
     */
    protected $hoursAccrued;

    /**
     * @var boolean
     */
    protected $modified;

    /**
     * @var UserInterface
     */
    protected $user;

    /**
     * @var SessionInterface
     */
    protected $session;

    /**
     * @param int $hoursAccrued
     */
    public function setHoursAccrued($hoursAccrued)
    {
        $this->hoursAccrued = $hoursAccrued;
    }

    /**
     * @return int
     */
    public function getHoursAccrued()
    {
        return $this->hoursAccrued;
    }

    /**
     * @param boolean $modified
     */
    public function setModified($modified)
    {
        $this->modified = $modified;
    }

    /**
     * @return boolean
     */
    public function isModified()
    {
        return $this->modified;
    }

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user)
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

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @return SessionInterface
     */
    public function getSession()
    {
        return $this->session;
    }
}
