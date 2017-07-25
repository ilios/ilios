<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\InstructorGroupsEntity;
use Ilios\CoreBundle\Traits\InstructorsEntity;
use Ilios\CoreBundle\Traits\LearnerGroupsEntity;
use Ilios\CoreBundle\Traits\LearnersEntity;
use Ilios\CoreBundle\Traits\SessionConsolidationEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TimestampableEntity;

/**
 * Class Offering
 *
 * @ORM\Table(name="offering",
 *   indexes={
 *     @ORM\Index(name="session_id_k", columns={"session_id"}),
 *     @ORM\Index(name="offering_dates_session_k", columns={"offering_id", "session_id", "start_date", "end_date"})
 *   }
 * )
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\OfferingRepository")
 *
 * @IS\Entity
 */
class Offering implements OfferingInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use TimestampableEntity;
    use SessionConsolidationEntity;
    use LearnerGroupsEntity;
    use LearnersEntity;
    use InstructorsEntity;
    use InstructorGroupsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="offering_id", type="integer")
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
     * @ORM\Column(name="room", type="string", length=255)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 255
     * )
     *
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $site;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $endDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="last_updated_on", type="datetime")
     *
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $updatedAt;

    /**
     * @var Session
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="Session", inversedBy="offerings")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
        $site = $this->getSite();
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
            'site' => $site,
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
}
