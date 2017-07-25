<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;
use Ilios\CoreBundle\Traits\StringableEntityInterface;

/**
 * Class AuditLogInterface
 *
 */
interface AuditLogInterface extends
    IdentifiableEntityInterface,
    StringableEntityInterface
{
    /**
     * Set action
     *
     * @param string $action
     */
    public function setAction($action);

    /**
     * Get action
     *
     * @return string
     */
    public function getAction();

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt();


    /**
     * @param \DateTime $createdAt
     */
    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Set objectId
     *
     * @param integer $objectId
     */
    public function setObjectId($objectId);

    /**
     * Get objectId
     *
     * @return integer
     */
    public function getObjectId();

    /**
     * Set objectClass
     *
     * @param string $objectClass
     */
    public function setObjectClass($objectClass);

    /**
     * Get objectClass
     *
     * @return string
     */
    public function getObjectClass();

    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();
}
