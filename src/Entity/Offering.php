<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearnersEntity;
use App\Traits\SessionConsolidationEntity;
use App\Attribute as IA;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\StringableIdEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TimestampableEntity;
use App\Repository\OfferingRepository;

/**
 * Class Offering
 */
#[ORM\Table(name: 'offering')]
#[ORM\Index(columns: ['session_id'], name: 'session_id_k')]
#[ORM\Index(columns: ['offering_id', 'session_id', 'start_date', 'end_date'], name: 'offering_dates_session_k')]
#[ORM\Entity(repositoryClass: OfferingRepository::class)]
#[IA\Entity]
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
     * @Assert\Type(type="integer")
     */
    #[ORM\Column(name: 'offering_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    #[IA\Expose]
    #[IA\Type('integer')]
    #[IA\OnlyReadable]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     */
    #[ORM\Column(name: 'room', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $room;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     */
    #[ORM\Column(name: 'site', type: 'string', length: 255, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $site;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      max = 2000,
     * )
     * @Assert\Url
     */
    #[ORM\Column(name: 'url', type: 'string', length: 2000, nullable: true)]
    #[IA\Expose]
    #[IA\Type('string')]
    protected $url;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'start_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $startDate;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'end_date', type: 'datetime')]
    #[IA\Expose]
    #[IA\Type('dateTime')]
    protected $endDate;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     */
    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    #[IA\Expose]
    #[IA\OnlyReadable]
    #[IA\Type('dateTime')]
    protected $updatedAt;

    /**
     * @var Session
     * @Assert\NotNull()
     */
    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'offerings')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    #[IA\Expose]
    #[IA\Type('entity')]
    protected $session;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_instructor_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_learner')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $learners;

    /**
     * @var ArrayCollection|UserInterface[]
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructedOfferings')]
    #[ORM\JoinTable(name: 'offering_x_instructor')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    #[IA\Expose]
    #[IA\Type('entityCollection')]
    protected $instructors;

    public function __construct()
    {
        $this->updatedAt = new DateTime();
        $this->learnerGroups = new ArrayCollection();
        $this->instructorGroups = new ArrayCollection();
        $this->learners = new ArrayCollection();
        $this->instructors = new ArrayCollection();
    }
    public function setRoom(?string $room)
    {
        $this->room = $room;
    }
    public function getRoom(): ?string
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

    public function getSite(): ?string
    {
        return $this->site;
    }
    public function setUrl(?string $url)
    {
        $this->url = $url;
    }
    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    public function getStartDate(): DateTime
    {
        return $this->startDate;
    }

    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    public function getEndDate(): DateTime
    {
        return $this->endDate;
    }

    public function setSession(SessionInterface $session)
    {
        $this->session = $session;
    }

    public function getSession(): ?SessionInterface
    {
        return $this->session;
    }

    public function getAllInstructors(): Collection
    {
        $instructors = $this->getInstructors()->toArray();
        foreach ($this->getInstructorGroups() as $group) {
            $instructors = array_merge($instructors, $group->getUsers()->toArray());
        }

        return new ArrayCollection($instructors);
    }

    public function getAlertProperties(): array
    {
        $instructorIds = $this->getInstructors()->map(fn(UserInterface $entity) => $entity->getId())->toArray();
        sort($instructorIds);
        $instructorGroupIds = $this->getInstructorGroups()->map(
            fn(InstructorGroupInterface $entity) => $entity->getId()
        )->toArray();
        sort($instructorGroupIds);
        $learnerIds = $this->getLearners()->map(fn(UserInterface $entity) => $entity->getId())->toArray();
        sort($learnerIds);
        $learnerGroupIds = $this->getLearnerGroups()->map(
            fn(LearnerGroupInterface $entity) => $entity->getId()
        )->toArray();
        sort($learnerGroupIds);
        $room = $this->getRoom();
        $site = $this->getSite();
        $url = $this->getUrl();
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
            'url' => $url,
        ];
    }

    public function getSchool(): ?SchoolInterface
    {
        if ($session = $this->getSession()) {
            return $session->getSchool();
        }
        return null;
    }
}
