<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class IlmSessionFacet
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="ilm_session_facet")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class IlmSessionFacet implements IlmSessionFacetInterface
{
//    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Column(name="ilm_session_facet_id", type="integer")
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
     * @ORM\Column(name="hours", type="decimal", precision=6, scale=2)
     */
    protected $hours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="date")
     */
    protected $dueDate;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", inversedBy="ilmSessionFacets")
     * @ORM\JoinTable(name="ilm_session_facet_x_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("learnerGroups")
     */
    protected $learnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="ilmSessionFacets")
     * @ORM\JoinTable(name="ilm_session_facet_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("instructorGroups")
     */
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructorIlmSessions")
     * @ORM\JoinTable(name="ilm_session_facet_x_instructor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $instructors;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="learnerIlmSessions")
     * @ORM\JoinTable(name="ilm_session_facet_x_learner",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $learners;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\OneToMany(targetEntity="Session", mappedBy="ilmSessionFacet")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->instructors = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
        $this->sessions = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->ilmSessionFacetId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->ilmSessionFacetId : $this->id;
    }

    /**
     * @param string $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return string
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return \DateTime
     */
    public function getDueDate()
    {
        return $this->dueDate;
    }

    /**
     * @param Collection $learnerGroups
     */
    public function setLearnerGroups(Collection $learnerGroups)
    {
        $this->learnerGroups = new ArrayCollection();

        foreach ($learnerGroups as $group) {
            $this->addLearnerGroup($group);
        }
    }

    /**
     * @param LearnerGroupInterface $learnerGroup
     */
    public function addLearnerGroup(LearnerGroupInterface $learnerGroup)
    {
        $this->learnerGroups->add($learnerGroup);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
    }

    /**
     * @param Collection $instructors
     */
    public function setInstructors(Collection $instructors)
    {
        $this->instructors = new ArrayCollection();

        foreach ($instructors as $instructor) {
            $this->addInstructor($instructor);
        }
    }

    /**
     * @param UserInterface $instructor
     */
    public function addInstructor(UserInterface $instructor)
    {
        $this->instructors->add($instructor);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructors()
    {
        return $this->instructors;
    }

    /**
     * @param Collection $instructorGroups
     */
    public function setInstructorGroups(Collection $instructorGroups)
    {
        $this->instructorGroups = new ArrayCollection();

        foreach ($instructorGroups as $instructorGroup) {
            $this->addInstructorGroup($instructorGroup);
        }
    }

    /**
     * @param InstructorGroupInterface $instructorGroup
     */
    public function addInstructorGroup(InstructorGroupInterface $instructorGroup)
    {
        $this->instructorGroups->add($instructorGroup);
    }

    /**
     * @return ArrayCollection|InstructorGroupInterface[]
     */
    public function getInstructorGroups()
    {
        return $this->instructorGroups;
    }

    /**
     * @param Collection $learners
     */
    public function setLearners(Collection $learners)
    {
        $this->learners = new ArrayCollection();

        foreach ($learners as $learner) {
            $this->addLearner($learner);
        }
    }

    /**
     * @param UserInterface $learner
     */
    public function addLearner(UserInterface $learner)
    {
        $this->learners->add($learner);
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getLearners()
    {
        return $this->learners;
    }

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions)
    {
        $this->sessions = new ArrayCollection();

        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions;
    }
}
