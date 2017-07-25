<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\ApiBundle\Annotation as IS;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;

/**
 * Class CurriculumInventoryReportDecorator
 *
 * @IS\DTO
 */
class CurriculumInventoryReportDecorator
{
    /**
     * @var int
     *
     * @IS\Expose
     * @IS\Type("integer")
     */
    protected $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $description;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $sequence;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    protected $endDate;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $program;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $report;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $export;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $academicLevels;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("entityCollection")
     */
    protected $sequenceBlocks;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    protected $absoluteFileUri;

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param Router $router
     */
    public function __construct(CurriculumInventoryReportInterface $report, Router $router)
    {
        $this->absoluteFileUri = $router->generate(
            'ilios_core_downloadcurriculuminventoryreport',
            ['token' => $report->getToken()],
            UrlGenerator::ABSOLUTE_URL
        );

        $this->id = $report->getId();
        $this->name = $report->getName();
        $this->description = $report->getDescription();
        $this->year = $report->getYear();
        $this->startDate = $report->getStartDate();
        $this->endDate = $report->getEndDate();
        $this->export = $report->getExport()?(string) $report->getExport():null;
        $this->sequence = $report->getSequence()?(string) $report->getSequence():null;
        $this->program = $report->getProgram()?(string) $report->getProgram():null;

        $sequenceBlockIds = $report->getSequenceBlocks()
            ->map(function (CurriculumInventorySequenceBlockInterface $block) {
                return (string) $block;
            });
        $this->sequenceBlocks = $sequenceBlockIds->toArray();

        $academicLevelIds = $report->getAcademicLevels()
            ->map(function (CurriculumInventoryAcademicLevelInterface $level) {
                return (string) $level;
            });
        $this->academicLevels = $academicLevelIds->toArray();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @return string
     */
    public function getSequence()
    {
        return $this->sequence;
    }

    /**
     * @return string
     */
    public function getYear()
    {
        return $this->year;
    }

    /**
     * @return \DateTime
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return \DateTime
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string
     */
    public function getProgram()
    {
        return $this->program;
    }

    /**
     * @return string
     */
    public function getReport()
    {
        return $this->report;
    }

    /**
     * @return string
     */
    public function getExport()
    {
        return $this->export;
    }

    /**
     * @return \string[]
     */
    public function getAcademicLevels()
    {
        return $this->academicLevels;
    }

    /**
     * @return \string[]
     */
    public function getSequenceBlocks()
    {
        return $this->sequenceBlocks;
    }

    /**
     * @return string
     */
    public function getAbsoluteFileUri()
    {
        return $this->absoluteFileUri;
    }
}
