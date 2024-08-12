<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\UpdateCommand;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\MeshDescriptorRepository;
use App\Repository\UserRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;

/**
 * @group cli
 */
class UpdateCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $userRepository;
    protected m\MockInterface $courseRepository;
    protected m\MockInterface $learningMaterialRepository;
    protected m\MockInterface $meshDescriptorRepository;
    protected m\MockInterface $messageBus;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->courseRepository = m::mock(CourseRepository::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->meshDescriptorRepository = m::mock(MeshDescriptorRepository::class);
        $this->messageBus = m::mock(MessageBusInterface::class);

        $command = new UpdateCommand(
            $this->userRepository,
            $this->courseRepository,
            $this->meshDescriptorRepository,
            $this->learningMaterialRepository,
            $this->messageBus,
        );
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
        unset($this->userRepository);
        unset($this->courseRepository);
        unset($this->learningMaterialRepository);
        unset($this->meshDescriptorRepository);
        unset($this->messageBus);
        unset($this->commandTester);
    }

    public function testUpdate(): void
    {
        $this->userRepository->shouldReceive('getIds')->once()->andReturn([1, 2]);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn([1, 2]);
        $this->meshDescriptorRepository->shouldReceive('getIds')->once()->andReturn([1, 2]);
//        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([1, 2]);

        $this->messageBus->shouldReceive('dispatch')
            ->times(3)
            //have to get creative with return as Symfony has marked Envelope as final
            ->andReturnUsing(fn() => new Envelope(m::mock(stdClass::class)));

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/2 users have been queued for indexing./',
            $output
        );
//        $this->assertMatchesRegularExpression(
//            '/2 learning materials have been queued for indexing./',
//            $output
//        );
        $this->assertMatchesRegularExpression(
            '/2 courses have been queued for indexing./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/2 descriptors have been queued for indexing./',
            $output
        );
    }
}
