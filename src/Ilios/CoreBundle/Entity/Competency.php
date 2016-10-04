<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ActivatableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class Competency
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="competency", indexes={@ORM\Index(name="parent_competency_id_k", columns={"parent_competency_id"})})
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\CompetencyRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Competency implements CompetencyInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use StringableIdEntity;
    use ActivatableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="competency_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
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
     * @JMS\Expose
     * @JMS\Type("string")
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
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("school")
     */
    protected $school;

    /**
    * @var ArrayCollection|ObjectiveInterface[]
    * @ORM\OneToMany(targetEntity="Objective", mappedBy="competency")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $parent;

    /**
     * @var ArrayCollection|CompetencyInterface[]
     *
     * @ORM\OneToMany(targetEntity="Competency", mappedBy="parent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
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
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("aamcPcrses")
     */
    protected $aamcPcrses;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="competencies")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
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
     * @JMS\Expose
     * @JMS\Type("boolean")
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
    public function setParent(CompetencyInterface $parent)
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
        $this->children->add($child);
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
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAamcPcrs(AamcPcrsInterface $aamcPcrs)
    {
        $this->aamcPcrses->removeElement($aamcPcrs);
    }

    /**
     * @return ArrayCollection|AamcPcrsInterface[]
     */
    public function getAamcPcrses()
    {
        return $this->aamcPcrses;
    }

    /**
     * @param Collection|ObjectiveInterface[] $objectives
     */
    public function setObjectives(Collection $objectives = null)
    {
        $this->objectives = new ArrayCollection();
        if (is_null($objectives)) {
            return;
        }

        foreach ($objectives as $objective) {
            $this->addObjective($objective);
        }
    }

    /**
     * @param ObjectiveInterface $objective
     */
    public function addObjective(ObjectiveInterface $objective)
    {
        $this->objectives->add($objective);
    }

    /**
     * @return ArrayCollection|ObjectiveInterface[]
     */
    public function getObjectives()
    {
        return $this->objectives;
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
}
