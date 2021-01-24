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
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $tableRowId;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $tableName;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public int $additionalText;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $dispatched;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("alertChangeTypes")
     * @IS\Type("array<string>")
     */
    public array $changeTypes = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $instigators = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("array<string>")
     */
    public array $recipients = [];

    public function __construct(
        int $id,
        int $tableRowId,
        int $tableName,
        int $additionalText,
        bool $dispatched
    ) {
        $this->id = $id;
        $this->tableRowId = $tableRowId;
        $this->tableName = $tableName;
        $this->additionalText = $additionalText;
        $this->dispatched = $dispatched;
    }
}
