<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearnersEntity;
use App\Traits\SessionConsolidationEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use DateTime;
use App\Repository\IlmSessionRepository;

/**
 * Class IlmSession
 * @IS\Entity
 */
#[ORM\Table(name: 'ilm_session_facet')]
#[ORM\Entity(repositoryClass: IlmSessionRepository::class)]
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
     * @Assert\Type(type="integer")
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'ilm_session_facet_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;
    /**
     * @var Session
     *      name="session_id",
     *      referencedColumnName="session_id",
     *      nullable=false,
     *      unique=true,
     *      onDelete="CASCADE"
     *   )
     * })
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\OneToOne(targetEntity: 'Session', inversedBy: 'ilmSession')]
    #[ORM\JoinColumn(
        name: 'session_id',
        referencedColumnName: 'session_id',
        nullable: false,
        unique: true,
        onDelete: 'CASCADE'
    )]
    protected $session;
    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     * @Assert\Length(
     *      min = 0,
     *      max = 10000
     * )
     * @IS\Expose
     * @IS\Type("float")
     */
    #[ORM\Column(name: 'hours', type: 'decimal', precision: 6, scale: 2)]
    protected $hours;
    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'due_date', type: 'datetime')]
    protected $dueDate;
    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learnerGroups;
    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructorGroups;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructorIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructors;
    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'learnerIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_learner')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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
     * @param DateTime $dueDate
     */
    public function setDueDate(DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }
    /**
     * @return DateTime
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
