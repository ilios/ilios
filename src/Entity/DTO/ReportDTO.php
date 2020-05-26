<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class ReportDTO
 *
 * @IS\DTO("reports")
 */
class ReportDTO
{
    /**
     * @var int
     * @IS\Id
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
    public $title;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $createdAt;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $subject;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $prepositionalObject;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $prepositionalObjectTableRowId;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
     */
    public $user;

    /**
     * Constructor
     */
    public function __construct(
        $id,
        $title,
        $createdAt,
        $subject,
        $prepositionalObject,
        $prepositionalObjectTableRowId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->subject = $subject;
        $this->prepositionalObject = $prepositionalObject;
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }
}
