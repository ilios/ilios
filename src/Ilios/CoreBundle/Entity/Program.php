<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;

/**
 * Class Program
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="program")
 * @ORM\Entity
 *
 * @JMS\ExclusionPolicy("all")
 */
class Program implements ProgramInterface
{
    use TitledEntity;
    use IdentifiableEntity;
    use StringableIdEntity;

    /**
     * @deprecated Replacde with trait in 3.x
     * @var int
     *
     * @ORM\Column(name="program_id", type="integer")
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
     * @ORM\Column(type="string", length=200, nullable=true)
     * @todo should be on the TitledEntity Trait
     * @var string
     *
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
     * @var string
     *
     * @ORM\Column(name="short_title", type="string", length=10)
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="string")
     * @Assert\Length(
     *      min = 1,
     *      max = 10
     * )
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("shortTitle")
     */
    protected $shortTitle;

    /**
     * @var int
     *
     * @ORM\Column(name="duration", type="smallint")
     *
     * @Assert\NotBlank()
     * @Assert\Type(type="integer")
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     */
    protected $duration;

    /**
     * @var boolean
     *
     * @ORM\Column(name="deleted", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $deleted;

    /**
     * @var boolean
     *
     * @ORM\Column(name="published_as_tbd", type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     * @JMS\SerializedName("publishedAsTbd")
     */
    protected $publishedAsTbd;

    /**
     * @var PublishEventInterface
     *
     * @ORM\ManyToOne(targetEntity="PublishEvent", inversedBy="programs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="publish_event_id", referencedColumnName="publish_event_id")
     * })
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("publishEvent")
     */
    protected $publishEvent;

    /**
    * @var SchoolInterface
    *
    * @ORM\ManyToOne(targetEntity="School", inversedBy="programs")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="owning_school_id", referencedColumnName="school_id")
    * })
    *
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\SerializedName("owningSchool")
    */
    protected $owningSchool;

    /**
    * @var ArrayCollection|ProgramYearInterface[]
    *
    * @ORM\OneToMany(targetEntity="ProgramYear", mappedBy="program")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("programYears")
    */
    protected $programYears;

    /**
    * @var ArrayCollection|CurriculumInventoryReportInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventoryReport", mappedBy="program")
    *
    * @JMS\Expose
    * @JMS\Type("array<string>")
    * @JMS\SerializedName("curriculumInventoryReports")
    */
    protected $curriculumInventoryReports;

        /**
     * Constructor
     */
    public function __construct()
    {
        $this->deleted = false;
        $this->publishedAsTbd = false;
    }

    /**
     * @param string $shortTitle
     */
    public function setShortTitle($shortTitle)
    {
        $this->shortTitle = $shortTitle;
    }

    /**
     * @return string
     */
    public function getShortTitle()
    {
        return $this->shortTitle;
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
     * @param boolean $deleted
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * @return boolean
     */
    public function isDeleted()
    {
        return $this->deleted;
    }

    /**
     * @param boolean $publishedAsTbd
     */
    public function setPublishedAsTbd($publishedAsTbd)
    {
        $this->publishedAsTbd = $publishedAsTbd;
    }

    /**
     * @return boolean
     */
    public function isPublishedAsTbd()
    {
        return $this->publishedAsTbd;
    }

    /**
     * @param SchoolInterface $school
     */
    public function setOwningSchool(SchoolInterface $school)
    {
        $this->owningSchool = $school;
    }

    /**
     * @return SchoolInterface
     */
    public function getOwningSchool()
    {
        return $this->owningSchool;
    }

    /**
     * @param PublishEventInterface $publishEvent
     */
    public function setPublishEvent(PublishEventInterface $publishEvent = null)
    {
        if ($publishEvent) {
            $publishEvent->setTableName('program');
            $publishEvent->setTableRowId($this->getId());
        }
        $this->publishEvent = $publishEvent;
    }

    /**
     * @return PublishEventInterface
     */
    public function getPublishEvent()
    {
        return $this->publishEvent;
    }

    /**
    * @param Collection $programYears
    */
    public function setProgramYears(Collection $programYears)
    {
        $this->programYears = new ArrayCollection();

        foreach ($programYears as $programYear) {
            $this->addProgramYear($programYear);
        }
    }

    /**
    * @param ProgramYearInterface $report
    */
    public function addProgramYear(ProgramYearInterface $programYear)
    {
        $this->programYears->add($programYear);
    }

    /**
    * @return ProgramYearInterface[]|ArrayCollection
    */
    public function getProgramYears()
    {
        return $this->programYears;
    }

    /**
    * @param Collection $curriculumInventoryReports
    */
    public function setCurriculumInventoryReports(Collection $reports)
    {
        $this->curriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addCurriculumInventoryReport($report);
        }
    }

    /**
    * @param CurriculumInventoryReportInterface $report
    */
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        $this->curriculumInventoryReports->add($report);
    }

    /**
    * @return CurriculumInventoryReportInterface[]|ArrayCollection
    */
    public function getCurriculumInventoryReports()
    {
        return $this->curriculumInventoryReports;
    }
}
