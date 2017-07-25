<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class AlertDTO
 * Data transfer object for an alert
 *
 * @IS\DTO
 */
class AlertDTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $tableRowId;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $tableName;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $additionalText;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $dispatched;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $changeTypes;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instigators;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
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
