<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearnersEntity;
use App\Traits\SessionConsolidationEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\StringableIdEntity;
use DateTime;
use App\Repository\IlmSessionRepository;

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

    #[ORM\Column(name: 'ilm_session_facet_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\OneToOne(inversedBy: 'ilmSession', targetEntity: 'Session')]
    #[ORM\JoinColumn(
        name: 'session_id',
        referencedColumnName: 'session_id',
        unique: true,
        nullable: false,
        onDelete: 'CASCADE'
    )]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotBlank]
    protected SessionInterface $session;

    #[ORM\Column(name: 'hours', type: 'decimal', precision: 6, scale: 2)]
    #[IA\Expose]
    #[IA\Type('float')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'numeric')]
    #[Assert\Length(min: 0, max: 10000)]
    protected string|float $hours;

    #[ORM\Column(name: 'due_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    #[Assert\NotBlank]
    protected DateTime $dueDate;

    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', inversedBy: 'ilmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_group')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

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
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructorGroups;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructorIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_instructor')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $instructors;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'learnerIlmSessions')]
    #[ORM\JoinTable(name: 'ilm_session_facet_x_learner')]
    #[ORM\JoinColumn(name: 'ilm_session_facet_id', referencedColumnName: 'ilm_session_facet_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learners;

    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->instructors = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
    }

    public function setHours(float $hours): void
    {
        $this->hours = $hours;
    }

    public function getHours(): float
    {
        //we have to type cast float because doctrine returns it as a string for precision
        return (float) $this->hours;
    }

    public function setDueDate(?DateTime $dueDate = null): void
    {
        $this->dueDate = $dueDate;
    }

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

    public function setSession(SessionInterface $session): void
    {
        $this->session = $session;
    }

    public function getSession(): SessionInterface
    {
        return $this->session;
    }

    public function getSchool(): ?SchoolInterface
    {
        return $this->session->getCourse()->getSchool();
    }
}
