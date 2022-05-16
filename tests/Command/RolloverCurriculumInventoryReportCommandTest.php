<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\RolloverCurriculumInventoryReportCommand;
use App\Entity\CurriculumInventoryReport;
use App\Entity\Program;
use App\Repository\CurriculumInventoryReportRepository;
use App\Service\CurriculumInventory\ReportRollover;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class RolloverCurriculumInventoryReportCommandTest
 * @group cli
 */
class RolloverCurriculumInventoryReportCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:rollover-ci-report';

    /**
     * @var m\MockInterface
     */

    protected $service;

    /**
     * @var m\MockInterface
     */
    protected $reportRepository;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->service = m::mock(ReportRollover::class);
        $this->reportRepository = m::mock(CurriculumInventoryReportRepository::class);
        $command = new RolloverCurriculumInventoryReportCommand($this->reportRepository, $this->service);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->service);
        unset($this->reportRepository);
        unset($this->commandTester);
    }

    public function testCommandFailsWithoutArguments()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
        ]);
    }

    public function testCommandPassesArgumentsAndDefaultOptions()
    {
        $reportId  = 1;
        $newReportId = 5;
        $report = new CurriculumInventoryReport();
        $report->setId($reportId);
        $program = new Program();
        $report->setProgram($program);

        $this->service->shouldReceive('rollover')->andReturnUsing(function () use ($newReportId) {
            $report = new CurriculumInventoryReport();
            $report->setId($newReportId);
            return $report;
        });

        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => $reportId])->andReturn($report);

        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            'reportId' => $reportId,
        ]);

        $this->service
            ->shouldHaveReceived('rollover')
            ->withArgs([$report, $program, null, null, null])
            ->once();
    }


    public function testCommandPassesUserProvidedOptions()
    {
        $options = [
            'name' => 'foo',
            'description' => 'lorem ipsum',
            'year' => 2016
        ];

        $reportId  = 1;
        $newReportId = 5;
        $report = new CurriculumInventoryReport();
        $report->setId($reportId);
        $program = new Program();
        $report->setProgram($program);

        $this->service->shouldReceive('rollover')->andReturnUsing(function () use ($newReportId) {
            $report = new CurriculumInventoryReport();
            $report->setId($newReportId);
            return $report;
        });

        $this->reportRepository->shouldReceive('findOneBy')->with(['id' => $reportId])->andReturn($report);

        $commandOptions = [
            'command' => self::COMMAND_NAME,
            'reportId' => $reportId,
        ];

        $this->commandTester->execute($commandOptions);
        foreach ($options as $name => $value) {
            $commandOptions['--' . $name] = $value;
        }

        $this->commandTester->execute($commandOptions);

        $this->service
            ->shouldHaveReceived('rollover')
            ->withArgs([$report, $program, $options['name'], $options['description'], $options['year']])
            ->once();
    }

    public function testCommandPrintsOutNewReportIdOnSuccess()
    {
        $reportId  = 1;
        $newReportId = 5;

        $this->service->shouldReceive('rollover')->andReturnUsing(function () use ($newReportId) {
            $report = new CurriculumInventoryReport();
            $report->setId($newReportId);
            return $report;
        });

        $this->reportRepository
            ->shouldReceive('findOneBy')->with(['id' => $reportId])->andReturnUsing(function () use ($reportId) {
                $report = new CurriculumInventoryReport();
                $report->setId($reportId);
                $program = new Program();
                $report->setProgram($program);
                return $report;
            });

        $this->commandTester->execute([
            'command' => self::COMMAND_NAME,
            'reportId' => $reportId,
        ]);

        $this->service
            ->shouldHaveReceived('rollover')
            ->withAnyArgs()
            ->once();

        $output = $this->commandTester->getDisplay();
        $this->assertEquals(
            "The given report has been rolled over. The new report id is {$newReportId}.",
            trim($output)
        );
    }
}
