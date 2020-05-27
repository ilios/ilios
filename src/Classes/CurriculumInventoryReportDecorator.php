<?php

declare(strict_types=1);

namespace App\Classes;

use App\Entity\CurriculumInventoryAcademicLevelInterface;
use App\Entity\CurriculumInventoryReportInterface;
use App\Entity\CurriculumInventorySequenceBlockInterface;
use App\Annotation as IS;
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
    public $id;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $name;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $description;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $sequence;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $year;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    public $startDate;

    /**
     * @var \DateTime
     *
     * @IS\Expose
     * @IS\ReadOnly
     * @IS\Type("dateTime")
     */
    public $endDate;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $program;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $report;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $export;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $academicLevels;

    /**
     * @var string[]
     *
     * @IS\Expose
     * @IS\Type("array<string>")
     */
    public $sequenceBlocks;

    /**
     * @var string
     *
     * @IS\Expose
     * @IS\Type("string")
     */
    public $absoluteFileUri;

    /**
     * @param CurriculumInventoryReportInterface $report
     * @param Router $router
     */
    public function __construct(CurriculumInventoryReportInterface $report, Router $router)
    {
        $this->absoluteFileUri = $router->generate(
            'ilios_downloadcurriculuminventoryreport',
            ['token' => $report->getToken()],
            UrlGenerator::ABSOLUTE_URL
        );

        $this->id = $report->getId();
        $this->name = $report->getName();
        $this->description = $report->getDescription();
        $this->year = $report->getYear();
        $this->startDate = $report->getStartDate();
        $this->endDate = $report->getEndDate();
        $this->export = $report->getExport() ? (string) $report->getExport() : null;
        $this->sequence = $report->getSequence() ? (string) $report->getSequence() : null;
        $this->program = $report->getProgram() ? (string) $report->getProgram() : null;

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
