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
 * @package Ilios\CoreBundle\Classes
 *
 * @IS\Entity
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
        $this->export = (string) $report->getExport();
        $this->sequence = (string) $report->getSequence();
        $this->program = (string) $report->getProgram();

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
}
