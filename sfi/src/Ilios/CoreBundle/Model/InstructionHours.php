<?php

namespace Ilios\CoreBundle\Model;

use Gedmo\Timestampable\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\IdentifiableTrait;


/**
 * InstructionHours
 */
class InstructionHours implements InstructionHoursInterface
{
    use IdentifiableTrait;
    use TimestampableEntity;

    /**
     * @var integer
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
     * @param integer $hoursAccrued
     */
    public function setHoursAccrued($hoursAccrued)
    {
        $this->hoursAccrued = $hoursAccrued;
    }

    /**
     * @return integer
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
