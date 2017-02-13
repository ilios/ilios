<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AlertDTO
 * Data transfer object for an alert
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AlertDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Type("integer")
     */
    public $tableRowId;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $tableName;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $additionalText;

    /**
     * @var int
     * @IS\Type("boolean")
     */
    public $dispatched;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $changeTypes;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $instigators;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $recipients;

    public function __construct(
        $id,
        $tableRowId,
        $tableName,
        $additionalText,
        $dispatched
    ) {
        $this->id = $id;
        $this->tableRowId = $tableRowId;
        $this->tableName = $tableName;
        $this->additionalText = $additionalText;
        $this->dispatched = $dispatched;

        $this->changeTypes = [];
        $this->instigators = [];
        $this->recipients = [];
    }
}
