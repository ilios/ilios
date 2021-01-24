<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class ReportDTO
 *
 * @IS\DTO("reports")
 */
class ReportDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $title;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $createdAt;

    /**
     * @IS\Expose
     * @IS\Related("schools")
     * @IS\Type("string")
     */
    public ?int $school = null;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $subject;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $prepositionalObject;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $prepositionalObjectTableRowId;

    /**
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("string")
     */
    public int $user;

    public function __construct(
        int $id,
        ?string $title,
        DateTime $createdAt,
        string $subject,
        ?string $prepositionalObject,
        ?string $prepositionalObjectTableRowId
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->createdAt = $createdAt;
        $this->subject = $subject;
        $this->prepositionalObject = $prepositionalObject;
        $this->prepositionalObjectTableRowId = $prepositionalObjectTableRowId;
    }
}
