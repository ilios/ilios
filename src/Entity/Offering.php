<?php

declare(strict_types=1);

namespace App\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use App\Traits\InstructorGroupsEntity;
use App\Traits\InstructorsEntity;
use App\Traits\LearnerGroupsEntity;
use App\Traits\LearnersEntity;
use App\Traits\SessionConsolidationEntity;
use App\Annotation as IS;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use App\Traits\StringableIdEntity;
use App\Traits\IdentifiableEntity;
use App\Traits\TimestampableEntity;
use App\Repository\OfferingRepository;

/**
 * Class Offering
 *   indexes={
 *   }
 * )
 * @IS\Entity
 */
#[ORM\Table(name: 'offering')]
#[ORM\Index(name: 'session_id_k', columns: ['session_id'])]
#[ORM\Index(name: 'offering_dates_session_k', columns: ['offering_id', 'session_id', 'start_date', 'end_date'])]
#[ORM\Entity(repositoryClass: OfferingRepository::class)]
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
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
     */
    #[ORM\Column(name: 'offering_id', type: 'integer')]
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: 'IDENTITY')]
    protected $id;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'room', type: 'string', length: 255, nullable: true)]
    protected $room;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\AtLeastOneOf({
     *     @Assert\Blank,
     *     @Assert\Length(min=1,max=255)
     * })
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'site', type: 'string', length: 255, nullable: true)]
    protected $site;

    /**
     * @var string
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      max = 2000,
     * )
     * @Assert\Url
     * @IS\Expose
     * @IS\Type("string")
     */
    #[ORM\Column(name: 'url', type: 'string', length: 2000, nullable: true)]
    protected $url;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'start_date', type: 'datetime')]
    protected $startDate;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'end_date', type: 'datetime')]
    protected $endDate;

    /**
     * @var DateTime
     * @Assert\NotBlank()
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    #[ORM\Column(name: 'last_updated_on', type: 'datetime')]
    protected $updatedAt;

    /**
     * @var Session
     * @Assert\NotNull()
     * })
     * @IS\Expose
     * @IS\Type("entity")
     */
    #[ORM\ManyToOne(targetEntity: 'Session', inversedBy: 'offerings')]
    #[ORM\JoinColumn(name: 'session_id', referencedColumnName: 'session_id', onDelete: 'CASCADE')]
    protected $session;

    /**
     * @var ArrayCollection|LearnerGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'LearnerGroup', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'group_id', referencedColumnName: 'group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learnerGroups;

    /**
     * @var ArrayCollection|InstructorGroupInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'InstructorGroup', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_instructor_group')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'instructor_group_id', referencedColumnName: 'instructor_group_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $instructorGroups;

    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'offerings')]
    #[ORM\JoinTable(name: 'offering_x_learner')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
    protected $learners;

    /**
     * @var ArrayCollection|UserInterface[]
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    #[ORM\ManyToMany(targetEntity: 'User', inversedBy: 'instructedOfferings')]
    #[ORM\JoinTable(name: 'offering_x_instructor')]
    #[ORM\JoinColumn(name: 'offering_id', referencedColumnName: 'offering_id', onDelete: 'CASCADE')]
    #[ORM\InverseJoinColumn(name: 'user_id', referencedColumnName: 'user_id')]
    #[ORM\OrderBy(['id' => 'ASC'])]
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

    /**
     * @return string
     */
    public function getSite()
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

    /**
     * @param DateTime $startDate
     */
    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @param DateTime $endDate
     */
    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }

    /**
     * @return DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
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
