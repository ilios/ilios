<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\IlmSessionsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\UsersEntity;
use App\Attributes as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\OfferingsEntity;
use App\Traits\SchoolEntity;
use App\Repository\InstructorGroupRepository;

#[ORM\Table(name: 'instructor_group')]
#[ORM\Entity(repositoryClass: InstructorGroupRepository::class)]
#[IA\Entity]
class InstructorGroup implements InstructorGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use SchoolEntity;
    use LearnerGroupsEntity;
    use UsersEntity;
    use IlmSessionsEntity;

    #[ORM\Column(name: 'instructor_group_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    #[Assert\Type(type: 'integer')]
    protected int $id;

    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    #[Assert\NotBlank]
    #[Assert\Type(type: 'string')]
    #[Assert\Length(min: 1, max: 60)]
    protected string $title;

    #[ORM\ManyToOne(targetEntity: 'School', inversedBy: 'instructorGroups')]
    #[ORM\JoinColumn(name: 'school_id', referencedColumnName: 'school_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    #[Assert\NotNull]
    protected SchoolInterface $school;

    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $learnerGroups;

    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $ilmSessions;

    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructorGroups')]
    #[ORM\JoinTable(name: 'instructor_group_x_user')]
    #[ORM\JoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $users;

    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'instructorGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type(IA\Type::ENTITY_COLLECTION)]
    protected Collection $offerings;

    public function __construct()
    {
        $this->learnerGroups = new ArrayCollection();
        $this->ilmSessions = new ArrayCollection();
        $this->users = new ArrayCollection();
        $this->offerings = new ArrayCollection();
    }

    public function addLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if (!$this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->add($learnerGroup);
            $learnerGroup->addInstructorGroup($this);
        }
    }

    public function removeLearnerGroup(LearnerGroupInterface $learnerGroup): void
    {
        if ($this->learnerGroups->contains($learnerGroup)) {
            $this->learnerGroups->removeElement($learnerGroup);
            $learnerGroup->removeInstructorGroup($this);
        }
    }

    public function addIlmSession(IlmSessionInterface $ilmSession): void
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addInstructorGroup($this);
        }
    }

    public function removeIlmSession(IlmSessionInterface $ilmSession): void
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeInstructorGroup($this);
        }
    }
}
