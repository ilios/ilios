<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\ObjectivesEntity;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;
use Ilios\CoreBundle\Traits\OfferingsEntity;
use Ilios\CoreBundle\Traits\DeletableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;

/**
 * Class Session
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="session",
 *   indexes={
 *     @ORM\Index(name="session_type_id_k", columns={"session_type_id"}),
 *     @ORM\Index(name="course_id_k", columns={"course_id"}),
 *     @ORM\Index(name="session_course_type_title_k", columns={"session_id", "course_id", "session_type_id", "title"}),
 *   }
 * )
 *
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Session implements SessionInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use OfferingsEntity;
    use DeletableEntity;
    use ObjectivesEntity;

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
     * @JMS\ReadOnly
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
     * @var IlmSessionInterface
     *
     * @ORM\OneToOne(targetEntity="IlmSession", mappedBy="session")
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("ilmSession")
     */
    protected $ilmSession;

    /**
     * @var ArrayCollection|TopicInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Topic", inversedBy="sessions")
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
    protected $topics;

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
    * @JMS\SerializedName("learningMaterials")
    */
    protected $learningMaterials;

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

        $this->topics = new ArrayCollection();
        $this->objectives = new ArrayCollection();
        $this->meshDescriptors = new ArrayCollection();
        $this->offerings = new ArrayCollection();
        $this->learningMaterials = new ArrayCollection();
        
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
        //only cascade offering delete
        if ($deleted) {
            foreach ($this->getOfferings() as $offering) {
                $offering->setDeleted(true);
            }
        }
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
     * @inheritdoc
     */
    public function getCourse()
    {
        if ($this->course && ! $this->course->isDeleted()) {
            return $this->course;
        }
        return null;
    }

    /**
     * @param IlmSessionInterface $ilmSession
     */
    public function setIlmSession(IlmSessionInterface $ilmSession)
    {
        $this->ilmSession = $ilmSession;
    }

    /**
     * @return IlmSessionInterface
     */
    public function getIlmSession()
    {
        return $this->ilmSession;
    }

    /**
     * @param Collection $topics
     */
    public function setTopics(Collection $topics)
    {
        $this->topics = new ArrayCollection();

        foreach ($topics as $topic) {
            $this->addTopic($topic);
        }
    }

    /**
     * @param TopicInterface $topic
     */
    public function addTopic(TopicInterface $topic)
    {
        if (!$this->topics->contains($topic)) {
            $this->topics->add($topic);
        }
    }

    /**
     * @return ArrayCollection|TopicInterface[]
     */
    public function getTopics()
    {
        return $this->topics;
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
     * @param Collection $learningMaterials
     */
    public function setLearningMaterials(Collection $learningMaterials = null)
    {
        $this->learningMaterials = new ArrayCollection();
        if (is_null($learningMaterials)) {
            return;
        }

        foreach ($learningMaterials as $learningMaterial) {
            $this->addLearningMaterial($learningMaterial);
        }
    }

    /**
     * @param SessionLearningMaterialInterface $learningMaterial
     */
    public function addLearningMaterial(SessionLearningMaterialInterface $learningMaterial)
    {
        $this->learningMaterials->add($learningMaterial);
    }

    /**
     * @return ArrayCollection|SessionLearningMaterialInterface[]
     */
    public function getLearningMaterials()
    {
        return $this->learningMaterials;
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($course = $this->getCourse()) {
            return $course->getSchool();
        }
        return null;
    }
}
