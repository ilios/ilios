<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\ListRootUsersCommand;
use Ilios\CliBundle\Command\UnsetRootUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Tests the Unset Root User command.
 *
 * Class UnsetRootUserCommandTest
 * @package Tests\CliBundle\Command
 */
class UnsetRootUserCommandTest extends \PHPUnit_Framework_TestCase
{
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

        $command = new UnsetRootUserCommand($this->userManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(UnsetRootUserCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        unset($this->userManager);
        unset($this->commandTester);
        m::close();
    }

    /**
     * @covers \Ilios\CliBundle\Command\UnsetRootUserCommand::execute
     */
    public function testSetRootUser()
    {
        $userId = 1;
        $user = m::mock('Ilios\CoreBundle\Entity\User');

        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userManager->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'command' => UnsetRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);

        $this->userManager->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ false ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("Root-level privileges have been revoked from user with id #{$userId}.", trim($output));
    }

    /**
     * @covers \Ilios\CliBundle\Command\UnsetRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->setExpectedException('RuntimeException', 'Not enough arguments (missing: "userId").');
        $this->commandTester->execute([
            'command' => UnsetRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \Ilios\CliBundle\Command\UnsetRootUserCommand::execute
     */
    public function testUserNotFound()
    {
        $userId = 0;
        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->setExpectedException('Exception', "No user with id #{$userId} was found.");
        $this->commandTester->execute([
            'command' => UnsetRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);
    }
}
