<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use DateTime;

/**
 * Class SessionDTO
 * Data transfer object for a session.
 */
#[IA\DTO('sessions')]
class SessionDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $title;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attireRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $equipmentRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $supplemental;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public ?bool $attendanceRequired;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $publishedAsTbd;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $published;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $instructionalNotes;

    #[IA\Expose]
    #[IA\Type('dateTime')]
    public DateTime $updatedAt;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $description;

    #[IA\Expose]
    #[IA\Related('sessionTypes')]
    #[IA\Type('integer')]
    public int $sessionType;

    #[IA\Expose]
    #[IA\Related('courses')]
    #[IA\Type('integer')]
    public int $course;

    #[IA\Expose]
    #[IA\Related('ilmSessions')]
    #[IA\Type('integer')]
    public ?int $ilmSession = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $terms = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessionObjectives = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $meshDescriptors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessionLearningMaterials')]
    #[IA\Type('array<string>')]
    public array $learningMaterials = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $administrators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $studentAdvisors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $offerings = [];

    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('integer')]
    public ?int $postrequisite = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('sessions')]
    #[IA\Type('array<string>')]
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
