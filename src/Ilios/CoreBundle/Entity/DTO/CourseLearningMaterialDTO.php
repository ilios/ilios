<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class CourseLearningMaterialDTO
 *
 * @IS\DTO
 */
class CourseLearningMaterialDTO
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $notes;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $required;

    /**
     * @var boolean
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    public $publicNotes;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $course;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $learningMaterial;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $meshDescriptors;

    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $position;

    /**
     * Needed for Voting, not exposed in the API
     * @var int
     *
     * @IS\Type("integer")
     */
    public $school;

    /**
     * Needed for Voting, not exposed in the API
     * @var int
     *
     * @IS\Type("integer")
     */
    public $status;

    /**
     * Needed for Voting, not exposed in the API
     * @var boolean
     *
     * @IS\Type("boolean")
     */
    public $courseIsLocked;

    /**
     * Needed for Voting, not exposed in the API
     * @var boolean
     *
     * @IS\Type("boolean")
     */
    public $courseIsArchived;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * Constructor
     */
    public function __construct($id, $notes, $required, $publicNotes, $position, $startDate, $endDate)
    {
        $this->id = $id;
        $this->notes = $notes;
        $this->required = $required;
        $this->publicNotes = $publicNotes;
        $this->position = $position;
        $this->startDate = $startDate;
        $this->endDate = $endDate;

        $this->meshDescriptors = [];
    }
}
