<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\MaterialCommand;
use App\Entity\DTO\LearningMaterialDTO;
use App\Repository\LearningMaterialRepository;
use App\Service\Index\LearningMaterials;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

#[Group('cli')]
class MaterialCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface | LearningMaterialRepository $repository;
    protected m\MockInterface | LearningMaterials $index;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(LearningMaterialRepository::class);
        $this->index = m::mock(LearningMaterials::class);

        $command = new MaterialCommand($this->repository, $this->index);
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
        unset($this->index);
        unset($this->commandTester);
    }

    public function testExecute(): void
    {
        $this->index->shouldReceive('isEnabled')->once()->andReturn(true);
        $dto = m::mock(LearningMaterialDTO::class);
        $this->repository->shouldReceive('findDTOBy')->once()->with(['id' => 13])->andReturn($dto);
        $this->index->shouldReceive('index')->once()->with([$dto], true);
        $this->commandTester->execute(['materialId' => 13]);

        $this->commandTester->assertCommandIsSuccessful();
    }

    public function testExecuteWithIndexDisabled(): void
    {
        $this->index->shouldReceive('isEnabled')->once()->andReturn(false);
        $this->index->shouldNotReceive('index');

        $this->commandTester->execute(['materialId' => 1]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Indexing is not currently configured./',
            $output
        );
    }
}
