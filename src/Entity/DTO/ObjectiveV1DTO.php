<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use App\Entity\Manager\CourseObjectiveManager;
use App\Entity\Manager\ProgramYearObjectiveManager;
use App\Entity\Manager\SessionObjectiveManager;
use App\Entity\DTO\ObjectiveDTO as CurrentDTO;

/**
 * Class ObjectiveDTO
 * Data transfer object for a Objective
 *
 * @IS\DTO
 */
class ObjectiveV1DTO
{
    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $title;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $competency;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $programYears;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $sessions;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $parents;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $children;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $meshDescriptors;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     *
     */
    public $ancestor;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     *
     */
    public $descendants;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("integer")
     * @deprecated
     *
     */
    public $position;

    /**
     * @var bool
     * @IS\Expose
     * @IS\Type("integer")
     *
     */
    public $active;


    public function __construct(
        CurrentDTO $dto,
        CourseObjectiveManager $courseObjectiveManager,
        ProgramYearObjectiveManager $programYearObjectiveManager,
        SessionObjectiveManager $sessionObjectiveManager
    ) {

        $this->id = $dto->id;
        $this->title = $dto->title;
        $this->active = $dto->active;

        $this->courses = [];
        $this->sessions = [];
        $this->programYears = [];

        if ($dto->courseObjectives) {
            $courseObjectiveDtos = $courseObjectiveManager->findDTOsBy(['id' => $dto->courseObjectives]);
            $this->courses = array_column($courseObjectiveDtos, 'course');
            $this->position = $courseObjectiveDtos[0]->position;
        }
        if ($dto->programYearObjectives) {
            $programYearObjectiveDtos = $programYearObjectiveManager->findDTOsBy(['id' => $dto->programYearObjectives]);
            $this->programYears = array_column($programYearObjectiveDtos, 'programYear');
            $this->position = $programYearObjectiveDtos[0]->position;
        }
        if ($dto->sessionObjectives) {
            $sessionObjectiveDtos = $sessionObjectiveManager->findDTOsBy(['id' => $dto->sessionObjectives]);
            $this->sessions = array_column($sessionObjectiveDtos, 'session');
            $this->position = $sessionObjectiveDtos[0]->position;
        }

        $this->parents = $dto->parents;
        $this->children = $dto->children;
        $this->meshDescriptors = $dto->meshDescriptors;
        $this->descendants = $dto->descendants;
    }
}
