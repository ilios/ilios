<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attribute as IA;
use App\Repository\SchoolRepository;

/**
 * Class SchoolDTO
 * Data transfer object for a school.
 */
#[IA\DTO('schools')]
#[IA\ExposeGraphQL]
class SchoolDTO
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
    public ?string $templatePrefix;

    #[IA\Expose]
    #[IA\Type('string')]
    public string $iliosAdministratorEmail;

    #[IA\Expose]
    #[IA\Type('string')]
    public ?string $changeAlertRecipients;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $competencies = [];

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
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $programs = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $vocabularies = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $instructorGroups = [];

    #[IA\Expose]
    #[IA\Related('curriculumInventoryInstitutions')]
    #[IA\Type('integer')]
    public ?int $curriculumInventoryInstitution = null;

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<string>')]
    public array $sessionTypes = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<string>')]
    public array $directors = [];

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
    #[IA\Related('schoolConfigs')]
    #[IA\Type('array<string>')]
    public array $configurations = [];

    public function __construct(
        int $id,
        string $title,
        ?string $templatePrefix,
        string $iliosAdministratorEmail,
        ?string $changeAlertRecipients
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->templatePrefix = $templatePrefix;
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;
        $this->changeAlertRecipients = $changeAlertRecipients;
    }
}
