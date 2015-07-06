<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class Session
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="session",
 *   indexes={
 *     @ORM\Index(name="session_type_id_k", columns={"session_type_id"}),
 *     @ORM\Index(name="course_id_k", columns={"course_id"}),
 *     @ORM\Index(name="session_course_type_title_k", columns={"session_id", "course_id", "session_type_id", "title"}),
 *     @ORM\Index(name="session_ibfk_3", columns={"ilm_session_facet_id"})
 *   }
 * )
 *
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Session implements SessionInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="session_id", type="integer")
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
     * @var boolean
     *
     * @ORM\Column(name="attire_required", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("attireRequired")
     */
    protected $attireRequired;

    /**
     * @var boolean
     *
     * @ORM\Column(name="equipment_required", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("equipmentRequired")
     */
    protected $equipmentRequired;

    /**
     * @var boolean
     *
     * @ORM\Column(name="supplemental", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $supplemental;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $deleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published_as_tbd", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    protected $publishedAsTbd;

    /**
     * @deprecated Replace with Timestampable trait in 3.x
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated_on", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("updatedAt")
     */
    protected $updatedAt;

    /**
     * @var SessionTypeInterface
     *
     * @ORM\ManyToOne(targetEntity="SessionType", inversedBy="sessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_type_id", referencedColumnName="session_type_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("sessionType")
     */
    protected $sessionType;

    /**
     * @var CourseInterface
     *
     * @ORM\ManyToOne(targetEntity="Course", inversedBy="sessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $course;

    /**
     * @var IlmSessionFacetInterface
     *
     * @ORM\ManyToOne(targetEntity="IlmSessionFacet", inversedBy="sessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ilmSessionFacet")
     */
    protected $ilmSessionFacet;

    /**
     * @var ArrayCollection|DisciplineInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Discipline", inversedBy="sessions")
     * @ORM\JoinTable(name="session_x_discipline",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="discipline_id", referencedColumnName="discipline_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $disciplines;

    /**
     * @var ArrayCollection|ObjectiveInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Objective", inversedBy="sessions")
     * @ORM\JoinTable(name="session_x_objective",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="objective_id", referencedColumnName="objective_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $objectives;

    /**
     * @var ArrayCollection|MeshDescriptorInterface[]
     *
     * @ORM\ManyToMany(targetEntity="MeshDescriptor", inversedBy="sessions")
     * @ORM\JoinTable(name="session_x_mesh",
     *   joinColumns={
     *     @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="mesh_descriptor_uid", referencedColumnName="mesh_descriptor_uid", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("meshDescriptors")
     */
    protected $meshDescriptors;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent", inversedBy="sessions")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("publishEvent")
     */
    protected $publishEvent;

    /**
     * @var SessionDescription
     *
     * @ORM\OneToOne(targetEntity="SessionDescription", mappedBy="session")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("sessionDescription")
     */
    protected $sessionDescription;

    /**
    * @var ArrayCollection|SessionLearningMaterialInterface[]
    *
    * @ORM\OneToMany(targetEntity="SessionLearningMaterial", mappedBy="session")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("sessionLearningMaterials")
    */
    protected $sessionLearningMaterials;

    /**
    * @var ArrayCollection|InstructionHoursInterface[]
    *
    * @ORM\OneToMany(targetEntity="InstructionHours", mappedBy="session")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("instructionHours")
    */
    protected $instructionHours;

    /**
    * @var ArrayCollection|OfferingInterface[]
    *
    * @ORM\OneToMany(targetEntity="Offering", mappedBy="session")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $offerings;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->attireRequired = false;
        $this->equipmentRequired = false;
        $this->supplemental = false;
        $this->deleted = false;
        $this->publishedAsTbd = false;

        $this->disciplines = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();

        $this->updatedAt = new \DateTime();
    }

    /**
     * @param boolean $attireRequired
     */
    public function setAttireRequired($attireRequired)
    {
        $this->attireRequired = $attireRequired;
    }

    /**
     * @return boolean
     */
    public function isAttireRequired()
    {
        return $this->attireRequired;
    }

    /**
     * @param boolean $equipmentRequired
     */
    public function setEquipmentRequired($equipmentRequired)
    {
        $this->equipmentRequired = $equipmentRequired;
    }

    /**
     * @return boolean
     */
    public function isEquipmentRequired()
    {
        return $this->equipmentRequired;
    }

    /**
     * @param boolean $supplemental
     */
    public function setSupplemental($supplemental)
    {
        $this->supplemental = $supplemental;
    }

    /**
     * @return boolean
     */
    public function isSupplemental()
    {
        return $this->supplemental;
    }

    /**
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    /**
     * @return boolean
     */
    public function isPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }

    /**
     * @param SessionTypeInterface $sessionType
     */
    public function setSessionType(SessionTypeInterface $sessionType)
    {
        $this->sessionType = $sessionType;
    }

    /**
     * @return SessionTypeInterface
     */
    public function getSessionType()
    {
        return $this->sessionType;
    }

    /**
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param IlmSessionFacetInterface $ilmSessionFacet
     */
    public function setIlmSessionFacet(IlmSessionFacetInterface $ilmSessionFacet)
    {
        $this->ilmSessionFacet = $ilmSessionFacet;
    }

    /**
     * @return IlmSessionFacetInterface
     */
    public function getIlmSessionFacet()
    {
        return $this->ilmSessionFacet;
    }

    /**
     * @param Collection $disciplines
     */
    public function setDisciplines(Collection $disciplines)
    {
        $this->disciplines = new ArrayCollection();

        foreach ($disciplines as $discipline) {
            $this->addDiscipline($discipline);
        }
    }

    /**
     * @param DisciplineInterface $discipline
     */
    public function addDiscipline(DisciplineInterface $discipline)
    {
        $this->disciplines->add($discipline);
    }

    /**
     * @return ArrayCollection|DisciplineInterface[]
     */
    public function getDisciplines()
    {
        return $this->disciplines;
    }

    /**
     * @param Collection $objectives
     */
    public function setObjectives(Collection $objectives)
    {
        $this->objectives = new ArrayCollection();

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
     * @param Collection $meshDescriptors
     */
    public function setMeshDescriptors(Collection $meshDescriptors)
    {
        $this->meshDescriptors = new ArrayCollection();

        foreach ($meshDescriptors as $meshDescriptor) {
            $this->addMeshDescriptor($meshDescriptor);
        }
    }

    /**
     * @param MeshDescriptorInterface $meshDescriptor
     */
    public function addMeshDescriptor(MeshDescriptorInterface $meshDescriptor)
    {
        $this->meshDescriptors->add($meshDescriptor);
    }

    /**
     * @return ArrayCollection|MeshDescriptorInterface[]
     */
    public function getMeshDescriptors()
    {
        return $this->meshDescriptors;
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent = null)
    {
        $this->publishEvent = $publishEvent;
    }

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }

    /**
     * @param SessionDescriptionInterface $sessionDescription
     */
    public function setSessionDescription(SessionDescriptionInterface $sessionDescription)
    {
        $this->sessionDescription = $sessionDescription;
    }

    /**
     * @return SessionDescription
     */
    public function getSessionDescription()
    {
        return $this->sessionDescription;
    }

    /**
    * @param Collection $offerings
    */
    public function setOfferings(Collection $offerings)
    {
        $this->offerings = new ArrayCollection();

        foreach ($offerings as $offering) {
            $this->addOffering($offering);
        }
    }

    /**
    * @param OfferingInterface $offering
    */
    public function addOffering(OfferingInterface $offering)
    {
        $this->offerings->add($offering);
    }

    /**
    * @return ArrayCollection|OfferingInterface[]
    */
    public function getOfferings()
    {
        return $this->offerings;
    }

    /**
     * @param Collection $sessionLearningMaterials
     */
    public function setSessionLearningMaterials(Collection $sessionLearningMaterials = null)
    {
        $this->sessionLearningMaterials = new ArrayCollection();
        if (is_null($sessionLearningMaterials)) {
            return;
        }

        foreach ($sessionLearningMaterials as $sessionLearningMaterial) {
            $this->addSessionLearningMaterial($sessionLearningMaterial);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $sessionLearningMaterial
     */
    public function addSessionLearningMaterial(SessionLearningMaterialInterface $sessionLearningMaterial)
    {
        $this->sessionLearningMaterials->add($sessionLearningMaterial);
    }

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getSessionLearningMaterials()
    {
        return $this->sessionLearningMaterials;
    }

    /**
     * @param Collection $instructionHours
     */
    public function setInstructionHours(Collection $instructionHours = null)
    {
        $this->instructionHours = new ArrayCollection();
        if (is_null($instructionHours)) {
            return;
        }

        foreach ($instructionHours as $instructionHour) {
            $this->addInstructionHour($instructionHour);
        }
    }

    /**
     * @param InstructionHoursInterface $instructionHour
     */
    public function addInstructionHour(InstructionHoursInterface $instructionHour)
    {
        $this->instructionHours->add($instructionHour);
    }

    /**
     * @return ArrayCollection|InstructionHoursInterface[]
     */
    public function getInstructionHours()
    {
        return $this->instructionHours;
    }
}
