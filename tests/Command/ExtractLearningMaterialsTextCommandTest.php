<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\ExtractLearningMaterialsTextCommand;
use App\Message\LearningMaterialTextExtractionRequest;
use App\Repository\LearningMaterialRepository;
use PHPUnit\Framework\Attributes\Group;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

#[Group('cli')]
class ExtractLearningMaterialsTextCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected LearningMaterialRepository|m\MockInterface $repository;
    protected MessageBusInterface|m\MockInterface $bus;

    public function setUp(): void
    {
        parent::setUp();
        $this->repository = m::mock(LearningMaterialRepository::class);
        $this->bus = m::mock(MessageBusInterface::class);

        $command = new ExtractLearningMaterialsTextCommand($this->repository, $this->bus);
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
        unset($this->bus);
        unset($this->commandTester);
    }

    public function testExtract(): void
    {
        $this->repository->shouldReceive('getFileLearningMaterialIds')->andReturn([1, 4]);
        $this->bus
            ->shouldReceive('dispatch')
            ->withArgs(fn (LearningMaterialTextExtractionRequest $r) => $r->getLearningMaterialIds() === [1, 4])
            ->andReturn(new Envelope(new stdClass()))
            ->once();
        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/2 learning materials have been queued for text extraction./',
            $output
        );
    }
}
