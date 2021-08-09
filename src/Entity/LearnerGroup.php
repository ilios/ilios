<?php

declare(strict_types=1);

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Traits\IlmSessionsEntity;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\UsersEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\IdentifiableEntity;
use App\Traits\TitledEntity;
use App\Traits\StringableIdEntity;
use App\Traits\OfferingsEntity;
use App\Repository\LearnerGroupRepository;

/**
 * Class LearnerGroup
 */
#[ORM\Table(name: '`group`')]
#[ORM\Entity(repositoryClass: LearnerGroupRepository::class)]
#[IA\Entity]
class LearnerGroup implements LearnerGroupInterface
{
    use IdentifiableEntity;
    use TitledEntity;
    use StringableIdEntity;
    use OfferingsEntity;
    use UsersEntity;
    use InstructorsEntity;
    use InstructorGroupsEntity;
    use IlmSessionsEntity;

    /**
     * @var int
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'group_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\ReadOnly]
    protected $id;

    /**
     * @var string
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 60
     * )
     */
    #[ORM\Column(type: 'string', length: 60)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $title;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=100)
     * })
     */
    #[ORM\Column(name: 'location', type: 'string', length: 100, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $location;

    /**
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      max = 2000,
     * )
     * @Assert\Url
     */
    #[ORM\Column(name: 'url', type: 'string', length: 2000, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected ?string $url;

    /**
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     */
    #[ORM\Column(name: 'needs_accommodation', type: 'boolean')]
    #[IA\Expose]
    #[IA\Type('boolean')]
    protected bool $needsAccommodation;

    /**
     * @var CohortInterface
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Cohort', inversedBy: 'learnerGroups')]
    #[ORM\JoinColumn(name: 'cohort_id', referencedColumnName: 'cohort_id', nullable: false, onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $cohort;

    /**
     * @var LearnerGroupInterface
     */
    #[ORM\ManyToOne(targetEntity: 'LearnerGroup', inversedBy: 'children')]
    #[ORM\JoinColumn(name: 'parent_group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $parent;

    /**
     * @var LearnerGroupInterface
     */
    #[ORM\ManyToOne(targetEntity: 'LearnerGroup', inversedBy: 'descendants')]
    #[ORM\JoinColumn(name: 'ancestor_id', referencedColumnName: 'group_id')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $ancestor;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'ancestor', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $descendants;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\OneToMany(mappedBy: 'parent', targetEntity: 'LearnerGroup')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $children;

    /**
     * @var ArrayCollection|IlmSessionInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'IlmSession', mappedBy: 'learnerGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $ilmSessions;

    /**
     * @var ArrayCollection|OfferingInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'Offering', mappedBy: 'learnerGroups')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $offerings;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'learnerGroups')]
    #[ORM\JoinTable(name: 'group_x_instructor_group')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
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
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'learnerGroups')]
    #[ORM\JoinTable(name: 'group_x_user')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $users;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructedLearnerGroups')]
    #[ORM\JoinTable(name: 'group_x_instructor')]
    #[ORM\JoinColumn(name: 'group_id', referencedColumnName: 'group_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id', onDelete: 'CASCADE')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructors;

    public function __construct()
    {
        $this->users            = new ArrayCollection();
        $this->ilmSessions      = new ArrayCollection();
        $this->offerings        = new ArrayCollection();
        $this->children         = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->instructors      = new ArrayCollection();
        $this->descendants      = new ArrayCollection();
        $this->needsAccommodation = false;
    }

    /**
     * @param string $location
     */
    public function setLocation($location)
    {
        $this->location = $location;
    }

    /**
     * @return string
     */
    public function getLocation()
    {
        return $this->location;
    }

    public function setCohort(CohortInterface $cohort)
    {
        $this->cohort = $cohort;
    }

    /**
     * @return CohortInterface
     */
    public function getCohort()
    {
        return $this->cohort;
    }

    public function addIlmSession(IlmSessionInterface $ilmSession)
    {
        if (!$this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->add($ilmSession);
            $ilmSession->addLearnerGroup($this);
        }
    }

    public function removeIlmSession(IlmSessionInterface $ilmSession)
    {
        if ($this->ilmSessions->contains($ilmSession)) {
            $this->ilmSessions->removeElement($ilmSession);
            $ilmSession->removeLearnerGroup($this);
        }
    }

    public function setParent(LearnerGroupInterface $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * @return LearnerGroupInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    public function setAncestor(LearnerGroupInterface $ancestor = null)
    {
        $this->ancestor = $ancestor;
    }

    /**
     * @return LearnerGroupInterface
     */
    public function getAncestor()
    {
        return $this->ancestor;
    }

    /**
     * If the objective has no ancestor then we need to objective itself
     *
     * @return LearnerGroupInterface
     */
    public function getAncestorOrSelf()
    {
        $ancestor = $this->getAncestor();

        return $ancestor ? $ancestor : $this;
    }

    public function setDescendants(Collection $descendants)
    {
        $this->descendants = new ArrayCollection();

        foreach ($descendants as $descendant) {
            $this->addDescendant($descendant);
        }
    }

    public function addDescendant(LearnerGroupInterface $descendant)
    {
        if (!$this->descendants->contains($descendant)) {
            $this->descendants->add($descendant);
            $descendant->setAncestor($this);
        }
    }

    public function removeDescendant(LearnerGroupInterface $descendant)
    {
        $this->descendants->removeElement($descendant);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getDescendants()
    {
        return $this->descendants;
    }

    public function setChildren(Collection $children = null)
    {
        $this->children = new ArrayCollection();
        if (is_null($children)) {
            return;
        }

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    public function addChild(LearnerGroupInterface $child)
    {
        if (!$this->children->contains($child)) {
            $this->children->add($child);
        }
    }

    public function removeChild(LearnerGroupInterface $child)
    {
        $this->children->removeElement($child);
    }

    /**
     * @return ArrayCollection|LearnerGroupInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @inheritdoc
     */
    public function addOffering(OfferingInterface $offering)
    {
        if (!$this->offerings->contains($offering)) {
            $this->offerings->add($offering);
            $offering->addLearnerGroup($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeOffering(OfferingInterface $offering)
    {
        if ($this->offerings->contains($offering)) {
            $this->offerings->removeElement($offering);
            $offering->removeLearnerGroup($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function getSchool()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort->getSchool();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProgram()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort = $cohort->getProgram();
        }
        return null;
    }

    /**
     * @inheritdoc
     */
    public function getProgramYear()
    {
        if ($cohort = $this->getCohort()) {
            return $cohort->getProgramYear();
        }
        return null;
    }

    /**
     * @inheritDoc
     */
    public function setNeedsAccommodation(bool $needsAccommodation): void
    {
        $this->needsAccommodation = $needsAccommodation;
    }

    /**
     * @inheritDoc
     */
    public function getNeedsAccommodation(): bool
    {
        return $this->needsAccommodation;
    }

    /**
     * @inheritDoc
     */
    public function setUrl(?string $url): void
    {
        $this->url = $url;
    }

    /**
     * @inheritDoc
     */
    public function getUrl(): ?string
    {
        return $this->url;
    }
}
