<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Interface PublishEventInterface
 * @package Ilios\CoreBundle\Entity
 */
interface PublishEventInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface
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
     */
    public function setTableName($tableName);

    /**
     * @return string
     */
    public function getTableName();

    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId);

    /**
     * @return int
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

    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses);

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course);

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses();

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions);

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session);

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions();
}
