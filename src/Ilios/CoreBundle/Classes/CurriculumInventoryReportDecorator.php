<?php

namespace Ilios\CoreBundle\Classes;

use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;
use Ilios\CoreBundle\Entity\CurriculumInventoryReportInterface;
use Ilios\CoreBundle\Entity\CurriculumInventorySequenceBlockInterface;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use JMS\Serializer\Annotation as JMS;
use Symfony\Component\Routing\Generator\UrlGenerator;
use Symfony\Bundle\FrameworkBundle\Routing\Router;


/**
 * Class CurriculumInventoryReportDecorator
 * @package Ilios\CoreBundle\Classes
 *
 * @JMS\ExclusionPolicy("all")
 */
class CurriculumInventoryReportDecorator
{
    /**
     * @var int
     *
     * @JMS\Expose
     * @JMS\Type("integer")
     * @JMS\SerializedName("id")
     */
    protected $id;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $name;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $description;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $year;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("startDate")
     */
    protected $startDate;

    /**
     * @var \DateTime
     *
     * @JMS\Expose
     * @JMS\ReadOnly
     * @JMS\Type("DateTime<'c'>")
     * @JMS\SerializedName("endDate")
     */
    protected $endDate;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $program;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $report;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     */
    protected $export;

    /**
     * @var string[]
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("academicLevels")
     */
    protected $academicLevels;

    /**
     * @var string[]
     *
     * @JMS\Expose
     * @JMS\Type("array<string>")
     * @JMS\SerializedName("sequenceBlocks")
     */
    protected $sequenceBlocks;

    /**
     * @var string
     *
     * @JMS\Expose
     * @JMS\Type("string")
     * @JMS\SerializedName("absoluteFileUri")
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
