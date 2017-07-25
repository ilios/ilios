<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\CoursesEntity;

/**
 * Class CourseClerkshipType
 *
 * @ORM\Table(name="course_clerkship_type")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CourseClerkshipTypeRepository")
 *
 * @IS\Entity
 */
class CourseClerkshipType implements CourseClerkshipTypeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use CoursesEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="course_clerkship_type_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    protected $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=20)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="clerkshipType")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setClerkshipType($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->setClerkshipType(null);
        }
    }
}
