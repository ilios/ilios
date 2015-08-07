<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;
use Ilios\CoreBundle\Traits\CoursesEntityInterface;
use Ilios\CoreBundle\Traits\SessionsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramYearsEntityInterface;
use Ilios\CoreBundle\Traits\ProgramsEntityInterface;

/**
 * Interface PublishEventInterface
 * @package Ilios\CoreBundle\Entity
 * @deprecated
 */
interface PublishEventInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface,
    CoursesEntityInterface,
    SessionsEntityInterface,
    ProgramYearsEntityInterface,
    ProgramsEntityInterface,
    LoggableEntityInterface
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
     * @param string $tableName
     * @deprecated
     */
    public function setTableName($tableName);

    /**
     * @return string
     * @deprecated
     */
    public function getTableName();

    /**
     * @param int $tableRowId
     * @deprecated
     */
    public function setTableRowId($tableRowId);

    /**
     * @return int
     * @deprecated
     */
    public function getTableRowId();

    /**
     * @param UserInterface $user
     */
    public function setAdministrator(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getAdministrator();
}
