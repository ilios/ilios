<?php
namespace Tests\App\Command;

use App\Command\RemoveRootUserCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use PHPUnit\Framework\TestCase;

/**
 * Tests the Remove Root User command.
 *
 * Class RemoveRootUserCommandTest
 */
class RemoveRootUserCommandTest extends TestCase
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
        $this->userManager = m::mock('AppBundle\Entity\Manager\UserManager');

        $command = new RemoveRootUserCommand($this->userManager);
        $application = new Application();
        $application->add($command);
        $commandInApp = $application->find(RemoveRootUserCommand::COMMAND_NAME);
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
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testRemoveRootUser()
    {
        $userId = 1;
        $user = m::mock('AppBundle\Entity\User');

        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $this->userManager->shouldReceive('update');
        $user->shouldReceive('setRoot');

        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);

        $this->userManager->shouldHaveReceived('update', [ $user, true, true ]);
        $user->shouldHaveReceived('setRoot', [ false ]);

        $output = $this->commandTester->getDisplay();

        $this->assertEquals("Root-level privileges have been revoked from user with id #{$userId}.", trim($output));
    }

    /**
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->expectException(\RuntimeException::class, 'Not enough arguments (missing: "userId").');
        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \App\Command\RemoveRootUserCommand::execute
     */
    public function testUserNotFound()
    {
        $userId = 0;
        $this->userManager->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn(null);

        $this->expectException(\Exception::class, "No user with id #{$userId} was found.");
        $this->commandTester->execute([
            'command' => RemoveRootUserCommand::COMMAND_NAME,
            'userId' => $userId
        ]);
    }
}
