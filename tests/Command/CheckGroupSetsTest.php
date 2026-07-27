<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CheckGroupSets;
use App\Entity\LearnerGroupInterface;
use App\Entity\UserInterface;
use App\Repository\LearnerGroupRepository;
use App\Repository\UserRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Group;
use RuntimeException;
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
    protected m\MockInterface $userRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->learnerGroupRepository = m::mock(LearnerGroupRepository::class);
        $this->userRepository = m::mock(UserRepository::class);
        $command = new CheckGroupSets($this->learnerGroupRepository, $this->userRepository);
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
        unset($this->userRepository);
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

        $this->commandTester->setInputs(['no']);
        $this->commandTester->execute([]);

        $this->assertEquals(Command::FAILURE, $this->commandTester->getStatusCode());

        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString('Errors found: 2', $output);
        $this->assertStringContainsString('Group', $output);
        $this->assertStringContainsString('Parent Group', $output);
        $this->assertStringContainsString('Missing Users', $output);
    }

    public function testFixWithOption(): void
    {
        $errors = $this->setupFixExpectations(2);
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn($errors);
        $this->learnerGroupRepository->shouldReceive('flushAndClear')->once();

        $this->commandTester->execute(['--fix' => true]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Added 2 missing user(s) across 2 parent group(s).',
            $output
        );
    }

    public function testFixOnConfirm(): void
    {
        $errors = $this->setupFixExpectations(2);
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn($errors);
        $this->learnerGroupRepository->shouldReceive('flushAndClear')->once();

        $this->commandTester->setInputs(['yes']);
        $this->commandTester->execute([]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Added 2 missing user(s) across 2 parent group(s).',
            $output
        );
    }

    public function testFixFlushesPerChunk(): void
    {
        $errors = $this->setupFixExpectations(26);
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn($errors);
        $this->learnerGroupRepository->shouldReceive('flushAndClear')->twice();

        $this->commandTester->execute(['--fix' => true]);

        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
        $output = $this->commandTester->getDisplay();
        $this->assertStringContainsString(
            'Added 26 missing user(s) across 26 parent group(s).',
            $output
        );
    }

    public function testFixThrowsWhenGroupNotFound(): void
    {
        $this->learnerGroupRepository->shouldReceive('findErrorsInGroupTrees')
            ->once()
            ->andReturn([
                ['id' => 3, 'parentId' => 1, 'missingFromParent' => [11]],
            ]);
        $this->learnerGroupRepository->shouldReceive('find')
            ->with(1)
            ->andReturn(null);

        $this->expectException(RuntimeException::class);
        $this->commandTester->execute(['--fix' => true]);
    }

    protected function setupFixExpectations(int $count): array
    {
        $errors = [];
        for ($i = 0; $i < $count; $i++) {
            $parentId = 100 + $i;
            $userId = 200 + $i;
            $group = m::mock(LearnerGroupInterface::class);
            $group->shouldReceive('addUser')->once();
            $this->learnerGroupRepository->shouldReceive('find')
                ->with($parentId)
                ->andReturn($group);
            $user = m::mock(UserInterface::class);
            $this->userRepository->shouldReceive('find')
                ->with($userId)
                ->andReturn($user);
            $this->learnerGroupRepository->shouldReceive('update')
                ->with($group, false)
                ->once();
            $errors[] = [
                'id' => 300 + $i,
                'parentId' => $parentId,
                'missingFromParent' => [$userId],
            ];
        }

        return $errors;
    }
}
