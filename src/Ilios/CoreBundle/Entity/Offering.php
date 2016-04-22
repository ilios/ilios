<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class Offering
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="offering",
 *   indexes={
 *     @ORM\Index(name="session_id_k", columns={"session_id"}),
 *     @ORM\Index(name="offering_dates_session_k", columns={"offering_id", "session_id", "start_date", "end_date"})
 *   }
 * )
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Offering implements OfferingInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="offering_id", type="integer")
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
     * @ORM\Column(name="room", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $room;

    /**
     * @var string
     *
     * @ORM\Column(name="site", type="string", length=255, nullable=true)
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $site;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     */
    protected $endDate;

    /**
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
     * @var Session
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="offerings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $session;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id")
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
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id")
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
     * @ORM\ManyToMany(targetEntity="User", inversedBy="offerings")
     * @ORM\JoinTable(name="offering_x_learner",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $learners;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="instructedOfferings")
     * @ORM\JoinTable(name="offering_x_instructor",
     *   joinColumns={
     *     @ORM\JoinColumn(name="offering_id", referencedColumnName="offering_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $instructors;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->updatedAt = new \DateTime();
        $this->learnerGroups = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
        $this->instructors = new ArrayCollection();
    }

    /**
     * @param string $room
     */
    public function setRoom($room)
    {
        $this->room = $room;
    }

    /**
     * @return string
     */
    public function getRoom()
    {
        return $this->room;
    }

    /**
     * @param string $site
     */
    public function setSite($site)
    {
        $this->site = $site;
    }

    /**
     * @return string
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * @param \DateTime $startDate
     */
    public function setStartDate(\DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param \DateTime $endDate
     */
    public function setEndDate(\DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return \DateTime
     */
    public function getUpdatedAt()
    {
        return $this->updatedAt;
    }

    /**
     * @param SessionInterface $session
     */
    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    /**
     * @inheritdoc
     */
    public function getSession()
    {
        return $this->session;
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
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
        }
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getLearnerGroups()
    {
        return $this->learnerGroups;
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
        if (! $this->instructorGroups->contains($instructorGroup)) {
            $this->instructorGroups->add($instructorGroup);
        }
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
        if (! $this->learners->contains($learner)) {
            $this->learners->add($learner);
        }
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getLearners()
    {
        return $this->learners;
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
        if (!$this->instructors->contains($instructor)) {
            $this->instructors->add($instructor);
        }
    }

    /**
     * @return ArrayCollection|UserInterface[]
     */
    public function getInstructors()
    {
        return $this->instructors;
    }

    /**
     * @inheritdoc
     */
    public function getAllInstructors()
    {
        $instructors = $this->getInstructors()->toArray();
        foreach ($this->getInstructorGroups() as $group) {
            $instructors = array_merge($instructors, $group->getUsers()->toArray());
        }

        return new ArrayCollection($instructors);
    }

    /**
     * {@inheritdoc}
     */
    public function getAlertProperties()
    {
        $instructorIds = $this->getInstructors()->map(function (UserInterface $entity) {
            return $entity->getId();
        })->toArray();
        sort($instructorIds);
        $instructorGroupIds = $this->getInstructorGroups()->map(function (InstructorGroupInterface $entity) {
            return $entity->getId();
        })->toArray();
        sort($instructorGroupIds);
        $learnerIds = $this->getLearners()->map(function (UserInterface $entity) {
            return $entity->getId();
        })->toArray();
        sort($learnerIds);
        $learnerGroupIds = $this->getLearnerGroups()->map(function (LearnerGroupInterface $entity) {
            return $entity->getId();
        })->toArray();
        sort($learnerGroupIds);
        $room = $this->getRoom();
        $startDate = $this->getStartDate()->getTimestamp();
        $endDate = $this->getEndDate()->getTimestamp();

        return [
            'instructors' => $instructorIds,
            'instructorGroups' => $instructorGroupIds,
            'learners' => $learnerIds,
            'learnerGroups' => $learnerGroupIds,
            'startDate' => $startDate,
            'endDate' => $endDate,
            'room' => $room,
        ];

    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($session = $this->getSession()) {
            return $session->getSchool();
        }
        return null;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return (string) $this->id;
    }
}
