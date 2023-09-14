<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Attributes as IA;
use OpenApi\Attributes as OA;

#[IA\DTO('schools')]
#[IA\ExposeGraphQL]
#[OA\Schema(
    title: "School",
    properties: [
        new OA\Property(
            "id",
            description: "ID",
            type: "integer",
            readOnly: true,
        ),
        new OA\Property(
            "title",
            description: "Title",
            type: "string"
        ),
        new OA\Property(
            "templatePrefix",
            description: "Template prefix",
            type: "string"
        ),
        new OA\Property(
            "iliosAdministratorEmail",
            description: "Ilios administrator email",
            type: "string"
        ),
        new OA\Property(
            "changeAlertRecipients",
            description: "Change alert recipients",
            type: "string"
        )
    ]
)]
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
    #[IA\Type('array<integer>')]
    public array $competencies = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $courses = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $programs = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
    public array $vocabularies = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related]
    #[IA\Type('array<integer>')]
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
    #[IA\Type('array<integer>')]
    public array $sessionTypes = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<integer>')]
    public array $directors = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('users')]
    #[IA\Type('array<integer>')]
    public array $administrators = [];

    /**
     * @var int[]
     */
    #[IA\Expose]
    #[IA\Related('schoolConfigs')]
    #[IA\Type('array<integer>')]
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
