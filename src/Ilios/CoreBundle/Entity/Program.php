<?php

namespace Ilios\CoreBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Ilios\CoreBundle\Traits\DirectorsEntity;
use Ilios\CoreBundle\Traits\PublishableEntity;
use Symfony\Component\Validator\Constraints as Assert;

use Ilios\ApiBundle\Annotation as IS;

use Ilios\CoreBundle\Traits\IdentifiableEntity;
use Ilios\CoreBundle\Traits\TitledEntity;
use Ilios\CoreBundle\Traits\StringableIdEntity;
use Ilios\CoreBundle\Traits\ProgramYearsEntity;
use Ilios\CoreBundle\Traits\SchoolEntity;

/**
 * Class Program
 *
 * @ORM\Table(name="program")
 * @ORM\Entity(repositoryClass="Ilios\CoreBundle\Entity\Repository\ProgramRepository")
 *
 * @IS\Entity
 */
class Program implements ProgramInterface
{
    use TitledEntity;
    use IdentifiableEntity;
    use StringableIdEntity;
    use ProgramYearsEntity;
    use SchoolEntity;
    use PublishableEntity;
    use DirectorsEntity;

    /**
     * @var int
     *
     * @ORM\Column(name="program_id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     *
     * @Assert\Type(type="integer")
     *
     * @IS\Expose
     * @IS\Type("integer")
     * @IS\ReadOnly
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("string")
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
     * @IS\Expose
     * @IS\Type("integer")
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
     * @IS\Expose
     * @IS\Type("boolean")
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
     * @IS\Expose
     * @IS\Type("boolean")
     */
    protected $published;

    /**
     * @var SchoolInterface
     *
     * @Assert\NotNull()
     *
     * @ORM\ManyToOne(targetEntity="School", inversedBy="programs")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="school_id", referencedColumnName="school_id", nullable=false)
     * })
     *
     * @IS\Expose
     * @IS\Type("entity")
     */
    protected $school;

    /**
    * @var ArrayCollection|ProgramYearInterface[]
    *
    * @ORM\OneToMany(targetEntity="ProgramYear", mappedBy="program")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
    protected $programYears;

    /**
    * @var ArrayCollection|CurriculumInventoryReportInterface[]
    *
    * @ORM\OneToMany(targetEntity="CurriculumInventoryReport", mappedBy="program")
    *
    * @IS\Expose
    * @IS\Type("entityCollection")
    */
    protected $curriculumInventoryReports;

    /**
     * @var ArrayCollection|UserInterface[]
     *
     * @ORM\ManyToMany(targetEntity="User", inversedBy="directedPrograms"))
     * @ORM\JoinTable(name="program_director",
     *   joinColumns={
     *     @ORM\JoinColumn(name="program_id", referencedColumnName="program_id", onDelete="CASCADE")
     *   },
     *   inverseJoinColumns={
     *     @ORM\JoinColumn(name="user_id", referencedColumnName="user_id", onDelete="CASCADE")
     *   }
     * )
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $directors;

        /**
     * Constructor
     */
    public function __construct()
    {
        $this->publishedAsTbd = false;
        $this->published = false;
        $this->programYears = new ArrayCollection();
        $this->curriculumInventoryReports = new ArrayCollection();
        $this->directors = new ArrayCollection();
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
        if (!$this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->add($report);
        }
    }

    /**
    * {@inheritdoc}
    */
    public function removeCurriculumInventoryReport(CurriculumInventoryReportInterface $report)
    {
        if ($this->curriculumInventoryReports->contains($report)) {
            $this->curriculumInventoryReports->removeElement($report);
        }
    }

    /**
    * {@inheritdoc}
    */
    public function getCurriculumInventoryReports()
    {
        return $this->curriculumInventoryReports;
    }

    /**
     * @inheritdoc
     */
    public function addDirector(UserInterface $director)
    {
        if (!$this->directors->contains($director)) {
            $this->directors->add($director);
            $director->addDirectedProgram($this);
        }
    }

    /**
     * @inheritdoc
     */
    public function removeDirector(UserInterface $director)
    {
        if ($this->directors->contains($director)) {
            $this->directors->removeElement($director);
            $director->removeDirectedProgram($this);
        }
    }
}
