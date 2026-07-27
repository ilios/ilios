<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CheckGroupSets;
use App\Repository\LearnerGroupRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 */
#[Group('cli')]
#[CoversClass(CheckGroupSets::class)]
final class CheckGroupSetsTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $learnerGroupRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->learnerGroupRepository = m::mock(LearnerGroupRepository::class);
        $command = new CheckGroupSets($this->learnerGroupRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find('ilios:check-group-sets');
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->commandTester);
        unset($this->learnerGroupRepository);
    }

    public function testNoErrors(): void
    {
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn([]);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $this->assertStringContainsString(
            'All groups have been correctly provisioned.',
            $this->commandTester->getDisplay()
        );
    }

    public function testErrorsFound(): void
    {
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn([
                ['id' => 3, 'parentId' => 1, 'missingFromParent' => [11, 12]],
                ['id' => 4, 'parentId' => 2, 'missingFromParent' => [13]],
            ]);

        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Errors found: 2', $output);
        $this->assertStringContainsString('Group', $output);
        $this->assertStringContainsString('Parent Group', $output);
        $this->assertStringContainsString('Missing Users', $output);
    }
}
