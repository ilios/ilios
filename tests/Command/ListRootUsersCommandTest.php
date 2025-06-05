<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Command\ListRootUsersCommand;
use App\Entity\DTO\UserDTO;
use App\Repository\UserRepository;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Tests the List Root Users command.
 *
 * Class ListRootUsersCommandTest
 */
#[Group('cli')]
#[CoversClass(ListRootUsersCommand::class)]
final class ListRootUsersCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);

        $command = new ListRootUsersCommand($this->userRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->commandTester);
    }

    public function testListRootUsers(): void
    {
        $users = [];
        $users[] = new UserDTO(
            1,
            'Hans',
            'Dampf',
            '',
            '',
            '111-111-1111',
            'hans@test.com',
            'franz@test.com',
            null,
            true,
            true,
            '',
            '',
            true,
            false,
            '',
            true
        );
        $users[] = new UserDTO(
            2,
            'Ilse',
            'Bilse',
            '',
            '',
            '222-222-2222',
            'ilse@test.com',
            'bilse@test.com',
            '',
            true,
            false,
            '',
            '',
            true,
            false,
            '',
            true
        );

        $this->userRepository->shouldReceive('findDTOsBy')->with(['root' => true])->andReturn($users);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();

        $this->assertMatchesRegularExpression(
            '/\| 1\s+| Hans\s+\| Dampf\s+\| hans@test.com\s+\| 111-111-1111\s+\| Yes\s+\|/',
            $output
        );
        $this->assertMatchesRegularExpression(
            '/\| 2\s+\| Ilse\s+\| Bilse\s+\|| ilse@test.com\s+\| 222-222-2222\s+ No\s+\|/',
            $output
        );
    }

    public function testListUsersNoResults(): void
    {
        $this->userRepository
            ->shouldReceive('findDTOsBy')
            ->with(['root' => true])
            ->andReturn([]);

        $this->commandTester->execute([]);
        $output = $this->commandTester->getDisplay();
        $this->assertEquals('No users with root-level privileges found.', trim($output));
    }
}
