<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;
use DateTime;

/**
 * Class SessionDTO
 * Data transfer object for a session.
 *
 * @IS\DTO("sessions")
 */
class SessionDTO
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
     * @IS\Type("boolean")
     */
    public ?bool $attireRequired;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public ?bool $equipmentRequired;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public ?bool $supplemental;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public ?bool $attendanceRequired;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $publishedAsTbd;

    /**
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public bool $published;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $instructionalNotes;

    /**
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public DateTime $updatedAt;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $description;

    /**
     * @IS\Expose
     * @IS\Related("sessionTypes")
     * @IS\Type("integer")
     */
    public int $sessionType;

    /**
     * @IS\Expose
     * @IS\Related("courses")
     * @IS\Type("integer")
     */
    public int $course;

    /**
     * @IS\Expose
     * @IS\Related("ilmSessions")
     * @IS\Type("integer")
     */
    public ?int $ilmSession = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $terms = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionObjectives = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $meshDescriptors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessionLearningMaterials")
     * @IS\Type("array<string>")
     */
    public array $learningMaterials = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $administrators = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $studentAdvisors = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $offerings = [];

    /**
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("integer")
     */
    public ?int $postrequisite = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("sessions")
     * @IS\Type("array<string>")
     */
    public array $prerequisites = [];

    /**
     * For Voter use, not public
     */
    public int $school;

    public function __construct(
        int $id,
        ?string $title,
        ?string $description,
        ?bool $attireRequired,
        ?bool $equipmentRequired,
        ?bool $supplemental,
        ?bool $attendanceRequired,
        bool $publishedAsTbd,
        bool $published,
        ?string $instructionalNotes,
        DateTime $updatedAt
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->description = $description;
        $this->attireRequired = $attireRequired;
        $this->equipmentRequired = $equipmentRequired;
        $this->supplemental = $supplemental;
        $this->attendanceRequired = $attendanceRequired;
        $this->publishedAsTbd = $publishedAsTbd;
        $this->published = $published;
        $this->instructionalNotes = $instructionalNotes;
        $this->updatedAt = $updatedAt;
    }
}
