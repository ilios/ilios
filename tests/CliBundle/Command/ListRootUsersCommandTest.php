<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\ListRootUsersCommand;
use Ilios\CoreBundle\Entity\DTO\UserDTO;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests the List Root Users command.
 *
 * Class ListRootUsersCommandTest
 */
class ListRootUsersCommandTest extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;
    /**
     * @var m\MockInterface
     */
    protected $userManager;

    /**
     * @var CommandTester
     */
    protected $commandTester;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->userManager = m::mock('Ilios\CoreBundle\Entity\Manager\UserManager');

        $command = new ListRootUsersCommand($this->userManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(ListRootUsersCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->commandTester);
    }

    /**
     * @covers \Ilios\CliBundle\Command\ListRootUsersCommand::execute
     */
    public function testListRootUsers()
    {
        $users = [];
        $users[] = new UserDTO(
            1,
            'Hans',
            'Dampf',
            '',
            '111-111-1111',
            'hans@test.com',
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
            '222-222-2222',
            'ilse@test.com',
            true,
            false,
            '',
            '',
            true,
            false,
            '',
            true
        );

        $this->userManager->shouldReceive('findDTOsBy')->with(['root' => true])->andReturn($users);

        $this->commandTester->execute([
            'command' => ListRootUsersCommand::COMMAND_NAME,
        ]);
        $output = $this->commandTester->getDisplay();

        $this->assertRegExp(
            '/\| 1\s+| Hans\s+\| Dampf\s+\| hans@test.com\s+\| 111-111-1111\s+\| Yes\s+\|/',
            $output
        );
        $this->assertRegExp(
            '/\| 2\s+\| Ilse\s+\| Bilse\s+\|| ilse@test.com\s+\| 222-222-2222\s+\ No\s+\|/',
            $output
        );
    }

    /**
     * @covers \Ilios\CliBundle\Command\ListRootUsersCommand::execute
     */
    public function testListUsersNoResults()
    {
        $this->userManager
            ->shouldReceive('findDTOsBy')
            ->with(['root' => true])
            ->andReturn([]);

        $this->commandTester->execute([
            'command' => ListRootUsersCommand::COMMAND_NAME,
        ]);
        $output = $this->commandTester->getDisplay();
        $this->assertEquals('No users with root-level privileges found.', trim($output));
    }
}
