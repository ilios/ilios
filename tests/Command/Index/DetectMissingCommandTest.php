<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\DetectMissingCommand;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
class DetectMissingCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface | LearningMaterialRepository $repository;
    protected m\MockInterface | LearningMaterials $materialIndex;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(LearningMaterialRepository::class);
        $this->materialIndex = m::mock(LearningMaterials::class);

        $command = new DetectMissingCommand($this->repository, $this->materialIndex);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->repository);
        unset($this->materialIndex);
        unset($this->commandTester);
    }

    public function testExecuteeWithIndexDisabled(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->materialIndex->shouldNotReceive('getAllIds');

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
    public function testExecuteWithIndexEnabled(): void
    {
        $this->materialIndex->shouldReceive('isEnabled')->once()->andReturn(true);
        $this->materialIndex->shouldReceive('getAllIds')->once()->andReturn([13]);
        $this->repository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([13, 14]);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/1 materials are missing from the index/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Materials: 14/',
            $output
        );
    }
}
