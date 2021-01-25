<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class MeshDescriptorDTO
 * Data transfer object for a MeSH descriptor.
 *
 * @IS\DTO("meshDescriptors")
 */
class MeshDescriptorDTO
{
    /**
     * @IS\Id
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $id;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $name;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $annotation;

    /**
     * @var DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $createdAt;

    /**
     * @var DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $updatedAt;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courses = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessionObjectives")
     * @IS\Type("array<string>")
     */
    public array $sessionObjectives = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("courseObjectives")
     * @IS\Type("array<string>")
     */
    public array $courseObjectives = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("programYearObjectives")
     * @IS\Type("array<string>")
     */
    public array $programYearObjectives = [];


    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessions = [];

    /**
     * @var string[]
     * @IS\Expose
     * @IS\Related("meshConcepts")
     * @IS\Type("array<string>")
     */
    public array $concepts = [];

    /**
     * @var string[]
     * @IS\Expose
     * @IS\Related("meshQualifiers")
     * @IS\Type("array<string>")
     */
    public array $qualifiers = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("meshTrees")
     * @IS\Type("array<string>")
     */
    public array $trees = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionLearningMaterials = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $courseLearningMaterials = [];

    /**
     * @IS\Expose
     * @IS\Related("meshPreviousIndexings")
     * @IS\Type("integer")
     */
    public ?int $previousIndexing = null;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $deleted;

    public function __construct(
        string $id,
        string $name,
        string $annotation,
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
