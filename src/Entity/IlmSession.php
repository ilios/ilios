<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearnersEntity;
use App\Traits\SessionConsolidationEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use DateTime;
use App\Repository\IlmSessionRepository;

/**
 * Class IlmSession
 */
#[ORM\Table(name: 'ilm_session_facet')]
#[ORM\Entity(repositoryClass: IlmSessionRepository::class)]
#[IA\Entity]
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
     */
    #[ORM\Column(name: 'ilm_session_facet_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var Session
     * @Assert\NotBlank()
     */
    #[ORM\OneToOne(targetEntity: 'Session', inversedBy: 'ilmSession')]
    #[ORM\JoinColumn(
        name: 'session_id',
        referencedColumnName: 'session_id',
        unique: true,
        nullable: false,
        onDelete: 'CASCADE'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $session;

    /**
     * @var float
     * @Assert\NotBlank()
     * @Assert\Type(type="numeric")
     * @Assert\Length(
     *      min = 0,
     *      max = 10000
     * )
     */
    #[ORM\Column(name: 'hours', type: 'decimal', precision: 6, scale: 2)]
    #[IA\Expose]
    #[IA\Type('float')]
    protected $hours;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'due_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $dueDate;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(
        name: 'instructor_group_id',
        referencedColumnName: 'instructor_group_id',
        onDelete: 'CASCADE'
    )]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructorIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructors;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'learnerIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_learner')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learners;

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
    public function getHours(): float
    {
        return $this->hours;
    }

    public function setDueDate(DateTime $dueDate = null)
    {
        $this->dueDate = $dueDate;
    }

    /**
     * @return DateTime
     */
    public function getDueDate(): DateTime
    {
        return $this->dueDate;
    }

    public function getAllInstructors(): Collection
    {
        $instructors = $this->getInstructors()->toArray();
        foreach ($this->getInstructorGroups() as $group) {
            $instructors = array_merge($instructors, $group->getUsers()->toArray());
        }

        return new ArrayCollection($instructors);
    }

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSession(): ?SessionInterface
    {
        return $this->session;
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($session = $this->getSession()) {
            if ($course = $session->getCourse()) {
                return $course->getSchool();
            }
        }

        return null;
    }
}
