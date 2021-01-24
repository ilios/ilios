<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class SessionLearningMaterialDTO
 *
 * @IS\DTO("sessionLearningMaterials")
 */
class SessionLearningMaterialDTO
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
    public ?string $notes;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $required;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $publicNotes;

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $session;

    /**
     * @IS\Expose
     * @IS\Related("learningMaterials")
     * @IS\Type("integer")
     */
    public int $learningMaterial;

    /**
     * @var string[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $meshDescriptors = [];

    /**
     * @IS\Expose
     * @IS\Type("integer")
     */
    public int $position;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("integer")
     */
    public int $course;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("integer")
     */
    public int $school;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("integer")
     */
    public int $status;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("boolean")
     */
    public bool $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @IS\Type("boolean")
     */
    public bool $courseIsArchived;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public ?DateTime $startDate;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public ?DateTime $endDate;

    public function __construct(
        int $id,
        ?string $notes,
        bool $required,
        bool $publicNotes,
        int $position,
        ?DateTime $startDate,
        ?DateTime $endDate
    ) {
        $this->id = $id;
        $this->notes = $notes;
        $this->required = $required;
        $this->publicNotes = $publicNotes;
        $this->position = $position;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }
}
