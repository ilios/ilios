<?php

declare(strict_types=1);

namespace App\Tests\Command\Index;

use App\Command\Index\UpdateCommand;
use App\Repository\CourseRepository;
use App\Repository\LearningMaterialRepository;
use App\Repository\MeshDescriptorRepository;
use App\Repository\UserRepository;
use Doctrine\DBAL\Connection;
use Doctrine\DBAL\Result;
use Doctrine\DBAL\Statement;
use Doctrine\ORM\EntityManagerInterface;
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
    protected m\MockInterface $entityManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->courseRepository = m::mock(CourseRepository::class);
        $this->learningMaterialRepository = m::mock(LearningMaterialRepository::class);
        $this->meshDescriptorRepository = m::mock(MeshDescriptorRepository::class);
        $this->messageBus = m::mock(MessageBusInterface::class);
        $this->entityManager = m::mock(EntityManagerInterface::class);

        $command = new UpdateCommand(
            $this->userRepository,
            $this->courseRepository,
            $this->meshDescriptorRepository,
            $this->learningMaterialRepository,
            $this->messageBus,
            $this->entityManager,
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

    public function testUpdateWithNoMessages(): void
    {
        $cn = m::mock(Connection::class);
        $st = m::mock(Statement::class);
        $r = m::mock(Result::class);

        $cn->shouldReceive('prepare')->times(3)->andReturn($st);
        $r->shouldReceive('rowCount')->times(3)->andReturn(0);

        $st->shouldReceive('bindValue')->once()->with('type', '%UserIndexRequest%');
        $st->shouldReceive('bindValue')->once()->with('type', '%CourseIndexRequest%');
//        $st->shouldReceive('bindValue')->once()->with('type', '%LearningMaterialIndexRequest%');
        $st->shouldReceive('bindValue')->once()->with('type', '%MeshDescriptorIndexRequest%');

        $st->shouldReceive('executeQuery')->times(3)->andReturn($r);

        $this->entityManager->shouldReceive('getConnection')->times(3)->andReturn($cn);


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
            '/2 MeSH descriptors have been queued for indexing./',
            $output
        );
    }

    public function testUpdateWithMessages(): void
    {
        $cn = m::mock(Connection::class);
        $st = m::mock(Statement::class);
        $r = m::mock(Result::class);

        $cn->shouldReceive('prepare')->times(3)->andReturn($st);
        $r->shouldReceive('rowCount')->times(3)->andReturn(24);

        $st->shouldReceive('bindValue')->once()->with('type', '%UserIndexRequest%');
        $st->shouldReceive('bindValue')->once()->with('type', '%CourseIndexRequest%');
//        $st->shouldReceive('bindValue')->once()->with('type', '%LearningMaterialIndexRequest%');
        $st->shouldReceive('bindValue')->once()->with('type', '%MeshDescriptorIndexRequest%');

        $st->shouldReceive('executeQuery')->times(3)->andReturn($r);

        $this->entityManager->shouldReceive('getConnection')->times(3)->andReturn($cn);


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
            '/Existing users have been removed from the queue and /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/2 users have been queued for indexing./',
            $output
        );
//        $this->assertMatchesRegularExpression(
//            '/Existing learning materails have been removed from the queue and /',
//            $output
//        );
//        $this->assertMatchesRegularExpression(
//            '/2 learning materials have been queued for indexing./',
//            $output
//        );
        $this->assertMatchesRegularExpression(
            '/Existing courses have been removed from the queue and /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/2 courses have been queued for indexing./',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/Existing MeSH descriptors have been removed from the queue and /',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/2 MeSH descriptors have been queued for indexing./',
            $output
        );
    }
}
