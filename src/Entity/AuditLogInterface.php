<?php

declare(strict_types=1);

namespace App\Entity;

use App\Traits\IdentifiableEntityInterface;
use App\Traits\StringableEntityInterface;

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
    public function getAction(): string;

    /**
     * Get createdAt
     *
     * @return \DateTime
     */
    public function getCreatedAt(): \DateTime;


    public function setCreatedAt(\DateTime $createdAt);

    /**
     * Set objectId
     *
     * @param string $objectId
     */
    public function setObjectId($objectId);

    /**
     * Get objectId
     *
     * @return string
     */
    public function getObjectId(): string;

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
    public function getObjectClass(): string;

    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser(): UserInterface;
}
