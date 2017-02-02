<?php

namespace Ilios\CoreBundle\Entity\DTO;

use JMS\Serializer\Annotation as JMS;

/**
 * Class SchoolDTO
 * Data transfer object for a school.
 * @package Ilios\CoreBundle\Entity\DTO

 */
class SchoolDTO
{
    /**
     * @var int
     * @JMS\Type("integer")
     */
    public $id;

    /**
     * @var string
     * @JMS\Type("string")
     */
    public $title;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("iliosAdministratorEmail")
     */
    public $iliosAdministratorEmail;

    /**
     * @var string
     * @JMS\Type("string")
     * @JMS\SerializedName("changeAlertRecipients")
     */
    public $changeAlertRecipients;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $competencies;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $courses;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $programs;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $departments;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $vocabularies;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorGroups")
     */
    public $instructorGroups;

    /**
     * @var int
     * @JMS\Type("string")
     * @JMS\SerializedName("curriculumInventoryInstitution")
     */
    public $curriculumInventoryInstitution;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sessionTypes")
     */
    public $sessionTypes;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $directors;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $administrators;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
     */
    public $stewards;

    /**
     * @var int[]
     * @JMS\Type("array<string>")
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
