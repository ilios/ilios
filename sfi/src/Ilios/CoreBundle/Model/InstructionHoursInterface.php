<?php

namespace Ilios\CoreBundle\Model;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityinterface;

/**
 * Interface InstructionHoursInterface
 */
interface InstructionHoursInterface extends IdentifiableEntityInterface, TimestampableEntityinterface
{
    /**
     * @param int $hoursAccrued
     */
    public function setHoursAccrued($hoursAccrued);

    /**
     * @return int
     */
    public function getHoursAccrued();

    /**
     * @param boolean $modified
     */
    public function setModified($modified);

    /**
     * @return boolean
     */
    public function isModified();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session);

    /**
     * @return SessionInterface
     */
    public function getSession();
}

