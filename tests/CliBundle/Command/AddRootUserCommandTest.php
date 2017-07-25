<?php
namespace Tests\CliBundle\Command;

use Ilios\CliBundle\Command\AddRootUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Add Root User command.
 *
 * Class AddRootUserCommandTest
 */
class AddRootUserCommandTest extends TestCase
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

        $command = new AddRootUserCommand($this->userManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(AddRootUserCommand::COMMAND_NAME);
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
     * @covers \Ilios\CliBundle\Command\AddRootUserCommand::execute
     */
    public function testAddRootUser()
    {
        $userId = 1;
        $user = m::mock('Ilios\CoreBundle\Entity\User');

        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userManager->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);

        $this->userManager->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ true ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("User with id #{$userId} has been granted root-level privileges.", trim($output));
    }

    /**
     * @covers \Ilios\CliBundle\Command\AddRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->expectException(\RuntimeException::class, 'Not enough arguments (missing: "userId").');
        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \Ilios\CliBundle\Command\AddRootUserCommand::execute
     */
    public function testUserNotFound()
    {
        $userId = 0;
        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->expectException(\Exception::class, "No user with id #{$userId} was found.");
        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);
    }
}
