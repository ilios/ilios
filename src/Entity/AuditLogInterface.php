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
     */
    public function getAction(): string;

    /**
     * Get createdAt
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
     */
    public function getObjectClass(): string;

    public function setUser(UserInterface $user);

    public function getUser(): UserInterface;
}
