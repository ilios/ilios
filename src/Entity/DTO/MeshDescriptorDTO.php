<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class MeshDescriptorDTO
 * Data transfer object for a MeSH descriptor.
 */
#[IA\DTO('meshDescriptors')]
#[IA\ExposeGraphQL]
class MeshDescriptorDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('string')]
    public string $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $name;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $annotation;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $createdAt;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $courses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessionObjectives')]
    #[IA\Type('array<string>')]
    public array $sessionObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('courseObjectives')]
    #[IA\Type('array<string>')]
    public array $courseObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('programYearObjectives')]
    #[IA\Type('array<string>')]
    public array $programYearObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessions = [];

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related('meshConcepts')]
    #[IA\Type('array<string>')]
    public array $concepts = [];

    /**
     * @var string[]
     */
    #[IA\Expose]
    #[IA\Related('meshQualifiers')]
    #[IA\Type('array<string>')]
    public array $qualifiers = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('meshTrees')]
    #[IA\Type('array<string>')]
    public array $trees = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessionLearningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $courseLearningMaterials = [];

    #[IA\Expose]
    #[IA\Related('meshPreviousIndexings')]
    #[IA\Type('integer')]
    public ?int $previousIndexing = null;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $deleted;

    public function __construct(
        string $id,
        string $name,
        ?string $annotation,
        DateTime $createdAt,
        DateTime $updatedAt,
        bool $deleted
    ) {
        $this->id = $id;
        $this->name = $name;
        $this->annotation = $annotation;
        $this->createdAt = $createdAt;
        $this->updatedAt = $updatedAt;
        $this->deleted = $deleted;
    }
}
