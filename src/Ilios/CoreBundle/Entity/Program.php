<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\PublishableEntity;
use Symfony\Component\Validator\Constraints as Assert;

use JMS\Serializer\Annotation as JMS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class Program
 * @package Ilios\CoreBundle\Entity
 *
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\ProgramRepository")
 *
 * @JMS\ExclusionPolicy("all")
 * @JMS\AccessType("public_method")
 */
class Program implements ProgramInterface
{
    use TitledEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use PublishableEntity;

    /**
     * @deprecated Replace with trait in 3.x
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
     * @ORM\Column(type="string", length=200, nullable=false)
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
     * @var string
     *
     * @ORM\Column(name="short_title", type="string", length=10, nullable=true)
     *
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
     * @var boolean
     *
     * @ORM\Column(type="boolean")
     *
     * @Assert\NotNull()
     * @Assert\Type(type="bool")
     *
     * @JMS\Expose
     * @JMS\Type("boolean")
     */
    protected $published;

    /**
    * @var SchoolInterface
    *
    * @ORM\ManyToOne(targetEntity="School", inversedBy="programs")
    * @ORM\JoinColumns({
    *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
    * })
    *
    * @JMS\Expose
    * @JMS\Type("string")
    * @JMS\SerializedName("school")
    */
    protected $school;

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
        $this->publishedAsTbd = false;
        $this->published = false;
        $this->programYears = new ArrayCollection();
        $this->curriculumInventoryReports = new ArrayCollection();
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
    * {@inheritdoc}
    */
    public function setCurriculumInventoryReports(Collection $reports)
    {
        $this->curriculumInventoryReports = new ArrayCollection();

        foreach ($reports as $report) {
            $this->addCurriculumInventoryReport($report);
        }
    }

    /**
    * {@inheritdoc}
    */
    public function addCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        $this->curriculumInventoryReports->add($report);
    }

    /**
    * {@inheritdoc}
    */
    public function getCurriculumInventoryReports()
    {
        return $this->curriculumInventoryReports;
    }
}
