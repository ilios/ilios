<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;

/**
 * Interface InstructionHoursInterface
 */
interface InstructionHoursInterface extends IdentifiableEntityInterface, TimestampableEntityInterface
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
