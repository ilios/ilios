<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramsEntityInterface;

/**
 * Interface PublishEventInterface
 * @package Ilios\CoreBundle\Entity
 */
interface PublishEventInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ProgramYearsEntityInterface,
    ProgramsEntityInterface
{
    /**
     * @param string $machineIp
     */
    public function setMachineIp($machineIp);

    /**
     * @return string
     */
    public function getMachineIp();

    /**
     * @param \DateTime $timeStamp
     */
    public function setTimeStamp(\DateTime $timeStamp);

    /**
     * @return \DateTime
     */
    public function getTimeStamp();

    /**
     * @param UserInterface $user
     */
    public function setAdministrator(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getAdministrator();
}
