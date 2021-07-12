<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class SessionLearningMaterialDTO
 */
#[IA\DTO('sessionLearningMaterials')]
class SessionLearningMaterialDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $notes;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $required;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $publicNotes;

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $session;

    #[IA\Expose]
    #[IA\Related('learningMaterials')]
    #[IA\Type('integer')]
    public int $learningMaterial;

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $meshDescriptors = [];

    #[IA\Expose]
    #[IA\Type('integer')]
    public int $position;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('integer')]
    public int $course;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('integer')]
    public int $school;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('integer')]
    public int $status;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    public bool $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     */
    #[IA\Type('boolean')]
    public bool $courseIsArchived;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public ?DateTime $startDate;

    #[IA\Expose]
    #[IA\Type('dateTime')]
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
