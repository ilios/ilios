<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\UpdateCommand;
use App\Message\CourseIndexRequest;
use App\Message\LearningMaterialIndexRequest;
use App\Message\MeshDescriptorIndexRequest;
use App\Message\UserIndexRequest;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\MeshDescriptorRepository;
use App\Repository\UserRepository;
use App\Service\Config;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\Group;
use stdClass;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Messenger\Envelope;
use Symfony\Component\Messenger\MessageBusInterface;
use Mockery as m;

#[Group('cli')]
final class UpdateCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface|UserRepository $userRepository;
    protected m\MockInterface|CourseRepository $courseRepository;
    protected m\MockInterface|MeshDescriptorRepository $descriptorRepository;
    protected m\MockInterface|LearningMaterialRepository $learningMaterialRepository;
    protected m\MockInterface|MessageBusInterface $messageBus;
    protected m\MockInterface|Config $config;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->courseRepository = m::mock(CourseRepository::class);
        $this->descriptorRepository = m::mock(MeshDescriptorRepository::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->messageBus = m::mock(MessageBusInterface::class);
        $this->config = m::mock(Config::class);

        $command = new UpdateCommand(
            $this->userRepository,
            $this->courseRepository,
            $this->descriptorRepository,
            $this->learningMaterialRepository,
            $this->messageBus,
            $this->config
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
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
        unset($this->descriptorRepository);
        unset($this->learningMaterialRepository);
        unset($this->messageBus);
        unset($this->config);
        unset($this->commandTester);
    }

    public function testQueuesUsers(): void
    {
        $userIds = [1, 2, 3, 4, 5];
        $this->userRepository->shouldReceive('getIds')->once()->andReturn($userIds);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([]);
        $this->config->shouldReceive('get')->with('learningMaterialsDisabled')->once()->andReturn(false);

        $this->messageBus->shouldReceive('dispatch')
            ->once()
            ->with(m::on(function (UserIndexRequest $message) use ($userIds) {
                return $message->getUserIds() === $userIds;
            }))
            ->andReturn(new Envelope(new stdClass()));

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/5 users have been queued for indexing./',
            $output
        );
    }

    public function testQueuesCourses(): void
    {
        $courseIds = [1, 2, 3];
        $this->userRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn($courseIds);
        $this->descriptorRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([]);
        $this->config->shouldReceive('get')->with('learningMaterialsDisabled')->once()->andReturn(false);

        $this->messageBus->shouldReceive('dispatch')
            ->once()
            ->with(m::on(function (CourseIndexRequest $message) use ($courseIds) {
                return $message->getCourseIds() === $courseIds;
            }))
            ->andReturn(new Envelope(new stdClass()));

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/3 courses have been queued for indexing./',
            $output
        );
    }

    public function testQueuesLearningMaterials(): void
    {
        $learningMaterialIds = [1, 2, 3, 4];
        $this->userRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->learningMaterialRepository
            ->shouldReceive('getFileLearningMaterialIds')
            ->once()
            ->andReturn($learningMaterialIds);
        $this->config->shouldReceive('get')->with('learningMaterialsDisabled')->once()->andReturn(false);

        $this->messageBus->shouldReceive('dispatch')
            ->once()
            ->with(m::on(function (LearningMaterialIndexRequest $message) use ($learningMaterialIds) {
                return $message->getIds() === $learningMaterialIds;
            }))
            ->andReturn(new Envelope(new stdClass()));

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/4 learning materials have been queued for indexing./',
            $output
        );
    }

    public function testQueuesMeshDescriptors(): void
    {
        $descriptorIds = [1, 2];
        $this->userRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('getIds')->once()->andReturn($descriptorIds);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->once()->andReturn([]);
        $this->config->shouldReceive('get')->with('learningMaterialsDisabled')->once()->andReturn(false);

        $this->messageBus->shouldReceive('dispatch')
            ->once()
            ->with(m::on(function (MeshDescriptorIndexRequest $message) use ($descriptorIds) {
                return $message->getDescriptorIds() === $descriptorIds;
            }))
            ->andReturn(new Envelope(new stdClass()));

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/2 descriptors have been queued for indexing./',
            $output
        );
    }

    public function testSkipsLearningMaterialsWhenDisabled(): void
    {
        $this->userRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->courseRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->descriptorRepository->shouldReceive('getIds')->once()->andReturn([]);
        $this->learningMaterialRepository->shouldReceive('getFileLearningMaterialIds')->never();
        $this->config->shouldReceive('get')->with('learningMaterialsDisabled')->once()->andReturn(true);

        $this->commandTester->execute([]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Learning Materials are disabled on this instance./',
            $output
        );
    }
}
