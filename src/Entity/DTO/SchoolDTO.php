<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class SchoolDTO
 * Data transfer object for a school.
 *
 * @IS\DTO("schools")
 */
class SchoolDTO
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
    public string $title;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $templatePrefix;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public string $iliosAdministratorEmail;

    /**
     * @IS\Expose
     * @IS\Type("string")
     */
    public ?string $changeAlertRecipients;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $competencies = [];

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
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $programs = [];

     /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $vocabularies = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $instructorGroups = [];

    /**
     * @IS\Expose
     * @IS\Related("curriculumInventoryInstitutions")
     * @IS\Type("integer")
     */
    public ?int $curriculumInventoryInstitution = null;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public array $sessionTypes = [];

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Related("users")
     * @IS\Type("array<string>")
     */
    public array $directors = [];

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
     * @IS\Related("schoolConfigs")
     * @IS\Type("array<string>")
     */
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
