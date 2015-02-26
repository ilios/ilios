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
 */
class CurriculumInventorySequenceBlock implements CurriculumInventorySequenceBlockInterface
{
//    use IdentifiableEntity;
    use DescribableEntity;
    use TitledEntity;
    use StringableIdEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
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
     * @todo should be on the TitledEntity Trait
     * @var string
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 200
     * )
     *
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
    */
    protected $description;

    /**
     * @var boolean
     *
     * @ORM\Column(name="required", type="boolean")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")
     *
     */
    protected $required;

    /**
     * @var boolean
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")
     *     
     * @ORM\Column(name="child_sequence_order", type="boolean")
     */
    protected $childSequenceOrder;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @ORM\Column(name="order_in_sequence", type="integer")
     */
    protected $orderInSequence;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *     
     * @ORM\Column(name="minimum", type="integer")
     */
    protected $minimum;

    /**
     * @var int
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *     
     * @ORM\Column(name="maximum", type="integer")
     */
    protected $maximum;

    /**
     * @var boolean
     *
     * @ORM\Column(name="track", type="boolean")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="bool")
     *     
     * this field is currently tinyint data type in the db but used like a boolean
     */
    protected $track;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="start_date", type="date", nullable=true)
     *
     * @Assert\NotBlank()
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end_date", type="date", nullable=true)
     *
     * @Assert\NotBlank()
     */
    protected $endDate;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="integer")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     */
    protected $duration;

    /**
     * @var CurriculumInventoryAcademicLevelInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryAcademicLevel", inversedBy="sequenceBlocks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="academic_level_id", referencedColumnName="academic_level_id")
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
     *   @ORM\JoinColumn(name="parent_sequence_block_id", referencedColumnName="sequence_block_id")
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
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventoryReport", inversedBy="sequenceBlocks")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="report_id", referencedColumnName="report_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
    * @var ArrayCollection|CurriculumInventorySequenceBlockSessionInterface[]
    *
    * @ORM\OneToMany(
    *   targetEntity="CurriculumInventorySequenceBlockSession",
    *   mappedBy="sequenceBlock"
    * )
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    */
    protected $sessions;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * @param int $id
     */
    public function setId($id)
    {
        $this->sequenceBlockId = $id;
        $this->id = $id;
    }

    /**
     * @return int
     */
    public function getId()
    {
        return ($this->id === null) ? $this->sequenceBlockId : $this->id;
    }

    /**
     * @param boolean $required
     */
    public function setRequired($required)
    {
        $this->required = $required;
    }

    /**
     * @return boolean
     */
    public function isRequired()
    {
        return $this->required;
    }

    /**
     * @param boolean $childSequenceOrder
     */
    public function setChildSequenceOrder($childSequenceOrder)
    {
        $this->childSequenceOrder = $childSequenceOrder;
    }

    /**
     * @return boolean
     */
    public function hasChildSequenceOrder()
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
    public function setStartDate($startDate)
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
    public function setEndDate($endDate)
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
     * @param CourseInterface $course
     */
    public function setCourse(CourseInterface $course)
    {
        $this->course = $course;
    }

    /**
     * @return CourseInterface
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
}
