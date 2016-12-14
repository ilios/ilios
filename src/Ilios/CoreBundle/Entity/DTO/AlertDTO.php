<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class AlertDTO
 * Data transfer object for an alert
 * @package Ilios\CoreBundle\Entity\DTO
 */
class AlertDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @JMS\Type("integer")
     * @JMS\SerializedName("tableRowId")
     */
    public $tableRowId;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("tableName")
     */
    public $tableName;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("additionalText")
     */
    public $additionalText;

    /**
     * @var int
     * @JMS\Type("boolean")
     */
    public $dispatched;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("changeTypes")
     */
    public $changeTypes;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $instigators;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
