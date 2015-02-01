<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class CourseClerkshipType
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="course_clerkship_type")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class CourseClerkshipType implements CourseClerkshipTypeInterface
{
    use IdentifiableEntity;
    use TitledEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_clerkship_type_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $title;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="clerkshipType")
     *
     * @JMS\Expose
     * @JMS\Type("array")
     */
    protected $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * @param Collection $courses
     */
    public function setCourses(Collection $courses)
    {
        $this->courses = new ArrayCollection();

        foreach ($courses as $course) {
            $this->addCourse($course);
        }
    }

    /**
     * @param CourseInterface $course
     */
    public function addCourse(CourseInterface $course)
    {
        $this->courses->add($course);
    }

    /**
     * @return ArrayCollection|CourseInterface[]
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
