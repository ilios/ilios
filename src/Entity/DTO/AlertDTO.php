<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class AlertDTO
 * Data transfer object for an alert
 *
 * @IS\DTO("alerts")
 */
class AlertDTO
{
    /**
     * @var int
     * @IS\Id
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
     * @IS\Related("alertChangeTypes")
     * @IS\Type("array<string>")
     */
    public $changeTypes;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public $instigators;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("schools")
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
