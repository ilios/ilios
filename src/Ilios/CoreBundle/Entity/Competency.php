<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ActivatableEntity;
use Ilios\CoreBundle\Traits\ObjectivesEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class Competency
 *
 * @ORM\Table(name="competency", indexes={@ORM\Index(name="parent_competency_id_k", columns={"parent_competency_id"})})
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CompetencyRepository")
 *
 * @IS\Entity
 */
class Competency implements CompetencyInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;
    use ObjectivesEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="competency_id", type="integer")
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
     * @ORM\Column(type="string", length=200, nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $title;

    /**
     * @var SchoolInterface
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="competencies")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id")
     * })
     *
     * @Assert\NotNull()
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
    * @var ArrayCollection|ObjectiveInterface[]
    * @ORM\OneToMany(targetEntity="Objective", mappedBy="competency")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
    protected $objectives;

    /**
     * @var CompetencyInterface
     *
     * @ORM\ManyToOne(targetEntity="Competency", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_competency_id", referencedColumnName="competency_id")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $parent;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Competency", mappedBy="parent")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $children;

    /**
     * @var ArrayCollection|AamcPcrsInterface[]
     *
     * @ORM\ManyToMany(targetEntity="AamcPcrs", inversedBy="competencies")
     * @ORM\JoinTable(name="competency_x_aamc_pcrs",
     *   joinColumns={
     *     @ORM\JoinColumn(name="competency_id", referencedColumnName="competency_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="pcrs_id", referencedColumnName="pcrs_id")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $aamcPcrses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="competencies")
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $programYears;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $active;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->aamcPcrses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @param CompetencyInterface $parent
     */
    public function setParent(CompetencyInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return CompetencyInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param CompetencyInterface $child
     */
    public function addChild(CompetencyInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @param CompetencyInterface $child
     */
    public function removeChild(CompetencyInterface $child)
    {
        $this->children->removeElement($child);
        $child->setParent(null);
    }

    /**
     * @return ArrayCollection|CompetencyInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @return bool
     */
    public function hasChildren()
    {
        return (!$this->children->isEmpty()) ? true : false;
    }

    /**
     * @param Collection $aamcPcrses
     */
    public function setAamcPcrses(Collection $aamcPcrses)
    {
        $this->aamcPcrses = new ArrayCollection();

        foreach ($aamcPcrses as $aamcPcrs) {
            $this->addAamcPcrs($aamcPcrs);
        }
    }

    /**
     * @param AamcPcrsInterface $aamcPcrs
     */
    public function addAamcPcrs(AamcPcrsInterface $aamcPcrs)
    {
        if (!$this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->add($aamcPcrs);
            $aamcPcrs->addCompetency($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs)
    {
        if ($this->aamcPcrses->contains($aamcPcrs)) {
            $this->aamcPcrses->removeElement($aamcPcrs);
            $aamcPcrs->removeCompetency($this);
        }
    }

    /**
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function getAamcPcrses()
    {
        return $this->aamcPcrses;
    }

    /**
     * @inheritdoc
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addCompetency($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeCompetency($this);
        }
    }
}
