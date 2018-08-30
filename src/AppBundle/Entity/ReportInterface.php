<?php

namespace AppBundle\Entity;

use AppBundle\Traits\IdentifiableEntityInterface;
use AppBundle\Traits\SchoolEntityInterface;
use AppBundle\Traits\TitledEntityInterface;

/**
 * Interface ReportInterface
 */
interface ReportInterface extends
    IdentifiableEntityInterface,
    TitledEntityInterface,
    SchoolEntityInterface,
    LoggableEntityInterface
{

    /**
     * @return \DateTime
     */
    public function getCreatedAt();

    /**
     * @param string $subject
     */
    public function setSubject($subject);

    /**
     * @return string
     */
    public function getSubject();

    /**
     * @param string $prepositionalObject
     */
    public function setPrepositionalObject($prepositionalObject);

    /**
     * @return string
     */
    public function getPrepositionalObject();

    /**
     * @param string $prepositionalObjectTableRowId
     */
    public function setPrepositionalObjectTableRowId($prepositionalObjectTableRowId);

    /**
     * @return string
     */
    public function getPrepositionalObjectTableRowId();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}
