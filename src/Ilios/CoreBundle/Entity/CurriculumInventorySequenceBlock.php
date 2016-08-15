<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use JMS\Serializer\Annotation as JMS;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class CurriculumInventorySequenceBlock
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="curriculum_inventory_sequence_block")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class CurriculumInventorySequenceBlock implements CurriculumInventorySequenceBlockInterface
{
    use IdentifiableEntity;
    use DescribableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="sequence_block_id", type="integer")
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
     * @ORM\Column(type="string", length=200)
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $title;

    /**
     * @ORM\Column(name="description", type="text", nullable=true)
     * @var string
     *
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 65000
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
    */
    protected $description;

    /**
     * @var integer
     *
     * @ORM\Column(name="required", type="integer")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="integer")
     *
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $required;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     * @Assert\Range(
     *      min = 1,
     *      max = 3,
     * )
     *
     * @ORM\Column(name="child_sequence_order", type="smallint")
     *
     * @JMS\Expose
     * @JMS\SerializedName("childSequenceOrder")
     * @JMS\Type("integer")
     */
    protected $childSequenceOrder;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="order_in_sequence", type="integer")
     *
     * @JMS\Expose
     * @JMS\SerializedName("orderInSequence")
     * @JMS\Type("integer")
     */
    protected $orderInSequence;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="minimum", type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $minimum;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="maximum", type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $maximum;

    /**
     * @var boolean
     *
     * @ORM\Column(name="track", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     *
     * this field is currently tinyint data type in the db but used like a boolean
     */
    protected $track;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     *
     * @JMS\Expose
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     */
    protected $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $duration;

    /**
     * @var CurriculumInventoryAcademicLevelInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryAcademicLevel", inversedBy="sequenceBlocks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *     name="academic_level_id",
     *     referencedColumnName="academic_level_id",
     *     nullable=false,
     *     onDelete="cascade"
     *   )
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("academicLevel")
     */
    protected $academicLevel;

    /**
     * @var CourseInterface
     *
     * @ORM\ManyToOne(targetEntity="Course")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="course_id", referencedColumnName="course_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $course;

    /**
     * @var CurriculumInventorySequenceBlockInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventorySequenceBlock", inversedBy="children")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(
     *     name="parent_sequence_block_id",
     *     referencedColumnName="sequence_block_id",
     *     onDelete="cascade"
     *   )
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $parent;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     *
     * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock", mappedBy="parent")
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $children;

    /**
     * @var CurriculumInventoryReportInterface
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryReport", inversedBy="sequenceBlocks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id", onDelete="cascade")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
     * @var ArrayCollection|SessionInterface[]
     *
     * @ORM\ManyToMany(targetEntity="Session", inversedBy="sequenceBlocks")
     * @ORM\JoinTable("curriculum_inventory_sequence_block_x_session",
     *   joinColumns={
     *     @ORM\JoinColumn(name="sequence_block_id", referencedColumnName="sequence_block_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="session_id", referencedColumnName="session_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     */
    protected $sessions;

    public function __construct()
    {
        $this->children = new ArrayCollection();
        $this->sessions = new ArrayCollection();
        $this->required = self::OPTIONAL;
        $this->track = false;
    }

    /**
     * @param int $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return int
     */
    public function getRequired()
    {
        return $this->required;
    }

    /**
     * @param int $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder)
    {
        $this->childSequenceOrder = $childSequenceOrder;
    }

    /**
     * @return int $childSequenceOrder
     */
    public function getChildSequenceOrder()
    {
        return $this->childSequenceOrder;
    }

    /**
     * @param int $orderInSequence
     */
    public function setOrderInSequence($orderInSequence)
    {
        $this->orderInSequence = $orderInSequence;
    }

    /**
     * @return int
     */
    public function getOrderInSequence()
    {
        return $this->orderInSequence;
    }

    /**
     * @param int $minimum
     */
    public function setMinimum($minimum)
    {
        $this->minimum = $minimum;
    }

    /**
     * @return int
     */
    public function getMinimum()
    {
        return $this->minimum;
    }

    /**
     * @param int $maximum
     */
    public function setMaximum($maximum)
    {
        $this->maximum = $maximum;
    }

    /**
     * @return int
     */
    public function getMaximum()
    {
        return $this->maximum;
    }

    /**
     * @param boolean $track
     */
    public function setTrack($track)
    {
        $this->track = $track;
    }

    /**
     * @return boolean
     */
    public function hasTrack()
    {
        return $this->track;
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
     * @param int $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }

    /**
     * @return int
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param CurriculumInventoryAcademicLevelInterface $academicLevel
     */
    public function setAcademicLevel(CurriculumInventoryAcademicLevelInterface $academicLevel)
    {
        $this->academicLevel = $academicLevel;
    }

    /**
     * @return CurriculumInventoryAcademicLevelInterface
     */
    public function getAcademicLevel()
    {
        return $this->academicLevel;
    }

    /**
     * @inheritdoc
     */
    public function setCourse(CourseInterface $course = null)
    {
        $this->course = $course;
    }

    /**
     * @inheritdoc
     */
    public function getCourse()
    {
        return $this->course;
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $parent
     */
    public function setParent(CurriculumInventorySequenceBlockInterface $parent)
    {
        $this->parent = $parent;
    }

    /**
     * @return CurriculumInventorySequenceBlockInterface
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @param Collection $children
     */
    public function setChildren(Collection $children)
    {
        $this->children = new ArrayCollection();

        foreach ($children as $child) {
            $this->addChild($child);
        }
    }

    /**
     * @param CurriculumInventorySequenceBlockInterface $child
     */
    public function addChild(CurriculumInventorySequenceBlockInterface $child)
    {
        $this->children->add($child);
    }

    /**
     * @return ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * @param CurriculumInventoryReportInterface $report
     */
    public function setReport(CurriculumInventoryReportInterface $report)
    {
        $this->report = $report;
    }

    /**
     * @return CurriculumInventoryReportInterface
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @param Collection $sessions
     */
    public function setSessions(Collection $sessions = null)
    {
        $this->sessions = new ArrayCollection();
        if (is_null($sessions)) {
            return;
        }
        foreach ($sessions as $session) {
            $this->addSession($session);
        }
    }

    /**
     * @param SessionInterface $session
     */
    public function addSession(SessionInterface $session)
    {
        $this->sessions->add($session);
    }

    /**
     * @return ArrayCollection|SessionInterface[]
     */
    public function getSessions()
    {
        return $this->sessions;
    }

    /**
     * {@inheritdoc}
     */
    public function getChildrenAsSortedList()
    {
        $children = $this->getChildren()->toArray();
        $sortStrategy = $this->getChildSequenceOrder();
        switch ($sortStrategy) {
            case self::ORDERED:
                usort($children, array(__CLASS__, 'compareSequenceBlocksWithOrderedStrategy'));
                break;
            case self::UNORDERED:
            case self::PARALLEL:
            default:
                usort($children, array(__CLASS__, 'compareSequenceBlocksWithDefaultStrategy'));
                break;
        }
        return $children;
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied criterion for comparison is the </pre>"orderInSequence</pre> property.
     *
     * @param CurriculumInventorySequenceBlockInterface $a
     * @param CurriculumInventorySequenceBlockInterface $b
     * @return int One of -1, 0, 1.
     */
    public static function compareSequenceBlocksWithOrderedStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ) {
        if ($a->getOrderInSequence() === $b->getOrderInSequence()) {
            return 0;
        }
        return ($a->getOrderInSequence() > $b->getOrderInSequence()) ? 1 : -1;
    }

    /**
     * Callback function for comparing sequence blocks.
     * The applied, ranked criteria for comparison are:
     * 1. "academic level"
     *      Numeric sort, ascending.
     * 2. "start date"
     *      Numeric sort on timestamps, ascending. NULL values will be treated as unix timestamp 0.
     * 3. "title"
     *    Alphabetical sort.
     * 4. "sequence block id"
     *    A last resort. Numeric sort, ascending.
     *
     * @param CurriculumInventorySequenceBlockInterface $a
     * @param CurriculumInventorySequenceBlockInterface $b
     * @return int One of -1, 0, 1.
     */
    public static function compareSequenceBlocksWithDefaultStrategy(
        CurriculumInventorySequenceBlockInterface $a,
        CurriculumInventorySequenceBlockInterface $b
    ) {
        // 1. academic level id
        if ($a->getAcademicLevel()->getLevel() > $b->getAcademicLevel()->getLevel()) {
            return 1;
        } elseif ($a->getAcademicLevel()->getLevel() < $b->getAcademicLevel()->getLevel()) {
            return -1;
        }

        // 2. start date
        $startDateA = $a->getStartDate() ? $a->getStartDate()->getTimestamp() : 0;
        $startDateB = $b->getStartDate() ? $b->getStartDate()->getTimestamp() : 0;

        if ($startDateA > $startDateB) {
            return 1;
        } elseif ($startDateA < $startDateB) {
            return -1;
        }

        // 3. title comparison
        $n = strcasecmp($a->getTitle(), $b->getTitle());
        if ($n) {
            return $n > 0 ? 1 : -1;
        }

        // 4. sequence block id comparison
        if ($a->getId() > $b->getId()) {
            return 1;
        } elseif ($a->getId() < $b->getId()) {
            return -1;
        }
        return 0;
    }
}
