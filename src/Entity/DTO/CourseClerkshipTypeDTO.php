<?php

declare(strict_types=1);

namespace App\Entity\DTO;

use App\Annotation as IS;

/**
 * Class CourseClerkshipTypeDTO
 * Data transfer object for a course clerkship types
 *
 * @IS\DTO("courseClerkshipTypes")
 */
class CourseClerkshipTypeDTO
{
    /**
     * @var int
     * @IS\Id
     * @IS\Expose
     * @IS\Type("integer")
     */
    public $id;

    /**
     * @var int
     * @IS\Expose
     * @IS\Type("string")
     */
    public $title;

    /**
     * @var int
     * @IS\Expose
     * @IS\Related
     * @IS\Type("array<string>")
     */
    public $courses;

    public function __construct($id, $title)
    {
        $this->id = $id;
        $this->title = $title;

        $this->courses = [];
    }
}
