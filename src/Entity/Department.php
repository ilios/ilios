<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\SchoolEntity;
use App\Traits\StewardedEntity;

/**
 * Class Department
 *
 * @ORM\Table(name="department")
 * @ORM\Entity(repositoryClass="App\Entity\Repository\DepartmentRepository")
 *
 * @IS\Entity
 */
class Department implements DepartmentInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use SchoolEntity;
    use StewardedEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="department_id", type="integer")
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
     * @ORM\Column(type="string", length=90)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 90
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
    */
    protected $title;

    /**
     * @var SchoolInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="departments")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
     * @var ArrayCollection|ProgramYearStewardInterface[]
     *
     * @ORM\OneToMany(targetEntity="ProgramYearSteward", mappedBy="department")
     * @ORM\OrderBy({"id" = "ASC"})
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $stewards;

    /**
     * @param int $id
     */

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->stewards = new ArrayCollection();
    }
}
