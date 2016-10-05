<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ActivatableEntity;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Validator\Constraints as Assert;
use Ilios\CoreBundle\Traits\CoursesEntity;
use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\SessionsEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class Term
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="term",
 *   uniqueConstraints={
 *     @ORM\UniqueConstraint(name="unique_term_title", columns={"vocabulary_id", "title", "parent_term_id"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\TermRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Term implements TermInterface
{
    use CoursesEntity;
    use DescribableEntity;
    use IdentifiableEntity;
    use ProgramYearsEntity;
    use SessionsEntity;
    use StringableIdEntity;
    use TitledEntity;
    use ActivatableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="term_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var ArrayCollection|CourseInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Course", mappedBy="terms")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $courses;

    /**
     *
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     *
     */
    protected $description;

    /**
     * @var TermInterface
     *
     * @ORM\ManyToOne(targetEntity="Term", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="parent_term_id", referencedColumnName="term_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $parent;

    /**
     * @var ArrayCollection|TermInterface[]
     *
     * @ORM\OneToMany(targetEntity="Term", mappedBy="parent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $children;

    /**
     * @var ArrayCollection|ProgramYearInterface[]
     *
     * @ORM\ManyToMany(targetEntity="ProgramYear", mappedBy="terms")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("programYears")
     */
    protected $programYears;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", mappedBy="terms")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=200, nullable=true)
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
     * @var VocabularyInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Vocabulary", inversedBy="terms")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="vocabulary_id", referencedColumnName="vocabulary_id", nullable=false)
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("vocabulary")
     */
    protected $vocabulary;

    /**
     * @var ArrayCollection|AamcResourceTypeInterface[]
     *
     * @ORM\ManyToMany(targetEntity="AamcResourceType", inversedBy="terms")
     * @ORM\JoinTable(name="term_x_aamc_resource_type",
     *   joinColumns={
     *     @ORM\JoinColumn(name="term_id", referencedColumnName="term_id")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="resource_type_id", referencedColumnName="resource_type_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("aamcResourceTypes")
     */
    protected $aamcResourceTypes;

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
        $this->aamcResourceTypes = new ArrayCollection();
        $this->courses = new ArrayCollection();
        $this->programYears = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->children = new ArrayCollection();
        $this->active = true;
    }

    /**
     * @inheritdoc
     */
    public function addCourse(CourseInterface $course)
    {
        if (!$this->courses->contains($course)) {
            $this->courses->add($course);
            $course->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeCourse(CourseInterface $course)
    {
        if ($this->courses->contains($course)) {
            $this->courses->removeElement($course);
            $course->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        if (!$this->programYears->contains($programYear)) {
            $this->programYears->add($programYear);
            $programYear->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeProgramYear(ProgramYearInterface $programYear)
    {
        if ($this->programYears->contains($programYear)) {
            $this->programYears->removeElement($programYear);
            $programYear->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function addSession(SessionInterface $session)
    {
        if (!$this->sessions->contains($session)) {
            $this->sessions->add($session);
            $session->addTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeSession(SessionInterface $session)
    {
        if ($this->sessions->contains($session)) {
            $this->sessions->removeElement($session);
            $session->removeTerm($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getVocabulary()
    {
        return $this->vocabulary;
    }

    /**
     * @inheritdoc
     */
    public function setVocabulary(VocabularyInterface $vocabulary)
    {
        $this->vocabulary = $vocabulary;
    }

    /**
     * @inheritdoc
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @inheritdoc
     */
    public function setParent(TermInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @inheritdoc
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @inheritdoc
     */
    public function addChild(TermInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeChild(TermInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @inheritdoc
     */
    public function hasChildren()
    {
        return (!$this->children->isEmpty()) ? true : false;
    }

    /**
     * @inheritdoc
     */
    public function setAamcResourceTypes(Collection $aamcResourceTypes)
    {
        $this->aamcResourceTypes = new ArrayCollection();

        foreach ($aamcResourceTypes as $aamcResourceType) {
            $this->addAamcResourceType($aamcResourceType);
        }
    }

    /**
     * @inheritdoc
     */
    public function addAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        if (!$this->aamcResourceTypes->contains($aamcResourceType)) {
            $this->aamcResourceTypes->add($aamcResourceType);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeAamcResourceType(AamcResourceTypeInterface $aamcResourceType)
    {
        $this->aamcResourceTypes->removeElement($aamcResourceType);
    }

    /**
     * @inheritdoc
     */
    public function getAamcResourceTypes()
    {
        return $this->aamcResourceTypes;
    }
}
