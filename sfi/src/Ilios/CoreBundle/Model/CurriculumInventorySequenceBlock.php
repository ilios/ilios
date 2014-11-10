<?php

namespace Ilios\CoreBundle\Model;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

use Ilios\CoreBundle\Traits\DescribableEntity;
use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;

/**
 * Class CurriculumInventorySequenceBlock
 * @package Ilios\CoreBundle\Model
 *
 * @ORM\Entity
 * @ORM\Table(name="curriculum_inventory_sequence_block")
 */
class CurriculumInventorySequenceBlock implements CurriculumInventorySequenceBlockInterface
{
//    use IdentifiableEntity;
    use DescribableEntity;
    use TitledEntity;

    /**
     * @deprecated To be removed in 3.1, replaced by ID by enabling trait.
     * @var int
     *
     * @ORM\Id
     * @ORM\Column(type="integer", length=10, name="sequence_block_id")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $sequenceBlockId;

    /**
     * @var int
     */
    protected $id;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=3)
     */
    protected $required;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=3, name="child_sequence_order")
     */
    protected $childSequenceOrder;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=10, name="order_in_sequence")
     */
    protected $orderInSequence;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11)
     */
    protected $minimum;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11)
     */
    protected $maximum;

    /**
     * @var boolean
     *
     * @ORM\Column(type="boolean", length=3)
     */
    protected $track;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="start_date")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="date", name="end_date")
     */
    protected $endDate;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", length=11)
     */
    protected $duration;

    /**
     * @var CurriculumInventoryAcademicLevelInterface
     *
     * @ORM\OneToMany(targetEntity="CurriculumInventoryAcademicLevel", mappedBy="curriculumInventorySequenceBlocks")
     * @ORM\JoinColumn(name="academic_level_id", referencedColumnName="academic_level_i")
     */
    protected $academicLevel;

    /**
     * @var CourseInterface
     *
     * @ORM\OneToMany(targetEntity="Course", mappedBy="curriculumInventorySequenceBlocks")
     */
    protected $course;

    /**
     * @var CurriculumInventorySequenceBlockInterface
     *
     * @ORM\ManyToOne(targetEntity="CurriculumInventorySequenceBlock", inversedBy="children")
     * @ORM\JoinColumn(name="parent_sequence_block_id", referencedColumnName="sequence_block_id")
     */
    protected $parent;

    /**
     * @var ArrayCollection|CurriculumInventorySequenceBlockInterface[]
     *
     * @ORM\OneToMany(targetEntity="CurriculumInventorySequenceBlock", mappedBy="parent")
     */
    protected $children;

    /**
     * @var CurriculumInventoryReportInterface
     */
    protected $report;

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
