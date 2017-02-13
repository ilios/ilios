<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class SchoolDTO
 * Data transfer object for a school.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class SchoolDTO
{
    /**
     * @var int
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $iliosAdministratorEmail;

    /**
     * @var string
     * @IS\Type("string")
     */
    public $changeAlertRecipients;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $competencies;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $courses;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $programs;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $departments;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $vocabularies;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $instructorGroups;

    /**
     * @var int
     * @IS\Type("string")
     */
    public $curriculumInventoryInstitution;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $sessionTypes;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $directors;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $administrators;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $stewards;

    /**
     * @var int[]
     * @IS\Type("entityCollection")
     */
    public $configurations;


    public function __construct(
        $id,
        $title,
        $iliosAdministratorEmail,
        $changeAlertRecipients
    ) {
        $this->id = $id;
        $this->title = $title;
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
