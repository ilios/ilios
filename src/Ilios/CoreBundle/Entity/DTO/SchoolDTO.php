<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class SchoolDTO
 * Data transfer object for a school.
 *
 * @IS\DTO
 */
class SchoolDTO
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
     */
    public $title;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $templatePrefix;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $iliosAdministratorEmail;

    /**
     * @var string
     * @IS\Expose
     * @IS\Type("string")
     */
    public $changeAlertRecipients;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $programs;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $departments;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $vocabularies;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $instructorGroups;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $curriculumInventoryInstitution;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $sessionTypes;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $directors;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $administrators;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $stewards;

    /**
     * @var int[]
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $configurations;


    public function __construct(
        $id,
        $title,
        $templatePrefix,
        $iliosAdministratorEmail,
        $changeAlertRecipients
    ) {
        $this->id = $id;
        $this->title = $title;
        $this->templatePrefix = $templatePrefix;
        $this->iliosAdministratorEmail = $iliosAdministratorEmail;
        $this->changeAlertRecipients = $changeAlertRecipients;

        $this->competencies = [];
        $this->courses = [];
        $this->programs = [];
        $this->departments = [];
        $this->vocabularies = [];
        $this->instructorGroups = [];
        $this->sessionTypes = [];
        $this->stewards = [];
        $this->directors = [];
        $this->administrators = [];
        $this->configurations = [];
    }
}
