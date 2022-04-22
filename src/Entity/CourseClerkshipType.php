<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Attribute as IA;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use App\Traits\TitledEntity;
use App\Traits\CoursesEntity;
use App\Repository\CourseClerkshipTypeRepository;

/**
 * Class CourseClerkshipType
 */
#[ORM\Table(name: 'course_clerkship_type')]
#[ORM\Entity(repositoryClass: CourseClerkshipTypeRepository::class)]
#[IA\Entity]
class CourseClerkshipType implements CourseClerkshipTypeInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TitledEntity;
    use CoursesEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'course_clerkship_type_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 20
     * )
     */
    #[ORM\Column(type: 'string', length: 20)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var ArrayCollection|CourseInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'clerkshipType', targetEntity: 'Course')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $courses;

    public function __construct()
    {
        $this->courses = new ArrayCollection();
    }

    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->setClerkshipType($this);
        }
    }

    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->setClerkshipType(null);
        }
    }
}
