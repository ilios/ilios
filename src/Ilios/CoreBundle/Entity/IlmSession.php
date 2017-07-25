<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Ilios\CoreBundle\Traits\InstructorGroupsEntity;
use Ilios\CoreBundle\Traits\InstructorsEntity;
use Ilios\CoreBundle\Traits\LearnerGroupsEntity;
use Ilios\CoreBundle\Traits\LearnersEntity;
use Ilios\CoreBundle\Traits\SessionConsolidationEntity;
use Ilios\ApiBundle\Annotation as IS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class IlmSession
 *
 * @ORM\Table(name="ilm_session_facet")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\IlmSessionRepository")
 *
 * @IS\Entity
 */
class IlmSession implements IlmSessionInterface
{
    use IdentifiableEntity;
    use StringableIdEntity;
    use SessionConsolidationEntity;
    use LearnerGroupsEntity;
    use LearnersEntity;
    use InstructorsEntity;
    use InstructorGroupsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="ilm_session_facet_id", type="integer")
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
     * @var Session
     *
     * @ORM\OneToOne(targetEntity="Session", inversedBy="ilmSession")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *      name="session_id",
     *      referencedColumnName="session_id",
     *      nullable=false,
     *      unique=true,
     *      onDelete="CASCADE"
     *   )
     * })
     * @Assert\NotBlank()
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $session;

    /**
     * @var float
     *
     * @ORM\Column(name="hours", type="decimal", precision=6, scale=2)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     * @Assert\Length(
     *      min = 0,
     *      max = 10000
     * )
     *
     * @IS\Expose
     * @IS\Type("float")
     */
    protected $hours;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="due_date", type="datetime")
     *
     * @Assert\NotBlank()
     * @Assert\DateTime()
     *
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    protected $dueDate;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     *
     * @ORM\ManyToMany(targetEntity="LearnerGroup", inversedBy="ilmSessions")
     * @ORM\JoinTable(name="ilm_session_facet_x_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="group_id", referencedColumnName="group_id", onDelete="CASCADE")
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
     * @ORM\ManyToMany(targetEntity="InstructorGroup", inversedBy="ilmSessions")
     * @ORM\JoinTable(name="ilm_session_facet_x_instructor_group",
     *   joinColumns={
     *     @ORM\JoinColumn(name="ilm_session_facet_id", referencedColumnName="ilm_session_facet_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="instructor_group_id", referencedColumnName="instructor_group_id", onDelete="CASCADE")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
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
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $learners;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->instructors = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
    }

    /**
     * @param float $hours
     */
    public function setHours($hours)
    {
        $this->hours = $hours;
    }

    /**
     * @return float
     */
    public function getHours()
    {
        return $this->hours;
    }

    /**
     * @param \DateTime $dueDate
     */
    public function setDueDate(\DateTime $dueDate = null)
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
    public function getSchool()
    {
        if ($session = $this->getSession()) {
            if ($course = $session->getCourse()) {
                return $course->getSchool();
            }
        }

        return null;
    }
}
