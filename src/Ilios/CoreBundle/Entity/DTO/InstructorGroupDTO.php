<?php

namespace Ilios\CoreBundle\Entity\DTO;

use Ilios\ApiBundle\Annotation as IS;

/**
 * Class InstructorGroupDTO
 * Data transfer object for an instructor group
 *
 * @IS\DTO
 */
class InstructorGroupDTO
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
    public $title;

    /**
     * @var integer
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $school;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $learnerGroups;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $ilmSessions;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $users;

    /**
     * @var array
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $offerings;

    /**
     * Constructor
     */
    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;

        $this->learnerGroups = [];
        $this->ilmSessions = [];
        $this->users = [];
        $this->offerings = [];
    }
}
