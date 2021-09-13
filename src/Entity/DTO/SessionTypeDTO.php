<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use App\Repository\SessionTypeRepository;

/**
 * Class SessionTypeDTO
 * Data transfer object for a session type
 */
#[IA\DTO('sessionTypes', SessionTypeRepository::class)]
class SessionTypeDTO
{
    #[IA\Id]
    #[IA\Expose]
    #[IA\Type('integer')]
    public int $id;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $title;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $calendarColor;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $active;

    #[IA\Expose]
    #[IA\Type('boolean')]
    public bool $assessment;

    #[IA\Expose]
    #[IA\Related('assessmentOptions')]
    #[IA\Type('entity')]
    public ?int $assessmentOption = null;

    #[IA\Expose]
    #[IA\Related('schools')]
    #[IA\Type('entity')]
    public int $school;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $aamcMethods = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessions = [];

    public function __construct(int $id, string $title, string $calendarColor, bool $assessment, bool $active)
    {
        $this->id = $id;
        $this->title = $title;
        $this->calendarColor = $calendarColor;
        $this->assessment = $assessment;
        $this->active = $active;
    }
}
