<?php

namespace Ilios\CoreBundle\Entity;

use Ilios\CoreBundle\Traits\IdentifiableEntityInterface;

/**
 * Interface PermissionInterface
 * @package Ilios\CoreBundle\Entity
 */
interface PermissionInterface extends IdentifiableEntityInterface
{
    /**
     * @param UserInterface $user
     */
    public function setUser(UserInterface $user);

    /**
     * @return UserInterface
     */
    public function getUser();

    /**
     * @param boolean $canRead
     */
    public function setCanRead($canRead);

    /**
     * @return boolean
     */
    public function hasCanRead();

    /**
     * @param boolean $canWrite
     */
    public function setCanWrite($canWrite);

    /**
     * @return boolean
     */
    public function hasCanWrite();

    /**
     * @param int $tableRowId
     */
    public function setTableRowId($tableRowId);

    /**
     * @return int
     */
    public function getTableRowId();

    /**
     * @param string $tableName
     */
    public function setTableName($tableName);

    /**
     * @return string
     */
    public function getTableName();
}
