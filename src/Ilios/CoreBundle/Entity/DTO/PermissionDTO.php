<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class PermissionDTO
 *
 * @IS\DTO
 */
class PermissionDTO
{
    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $canRead;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $canWrite;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $tableRowId;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $tableName;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $user;

    /**
     * PermissionDTO constructor.
     * @param $id
     * @param $canRead
     * @param $canWrite
     * @param $tableRowId
     * @param $tableName
     */
    public function __construct($id, $canRead, $canWrite, $tableRowId, $tableName)
    {
        $this->id = $id;
        $this->canRead = $canRead;
        $this->canWrite = $canWrite;
        $this->tableRowId = $tableRowId;
        $this->tableName = $tableName;
    }
}
