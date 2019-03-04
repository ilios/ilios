<?php
namespace App\Tests\Command;

use App\Command\AddRootUserCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Tests the Add Root User command.
 *
 * Class AddRootUserCommandTest
 */
class AddRootUserCommandTest extends KernelTestCase
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
        $this->userManager = m::mock('App\Entity\Manager\UserManager');

        $command = new AddRootUserCommand($this->userManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(AddRootUserCommand::COMMAND_NAME);
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * @inheritdoc
     */
    public function tearDown() : void
    {
        unset($this->userManager);
        unset($this->commandTester);
    }

    /**
     * @covers \App\Command\AddRootUserCommand::execute
     */
    public function testAddRootUser()
    {
        $userId = 1;
        $user = m::mock('App\Entity\User');

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
     * @covers \App\Command\AddRootUserCommand::execute
     */
    public function testMissingInput()
    {
        $this->expectException(\RuntimeException::class, 'Not enough arguments (missing: "userId").');
        $this->commandTester->execute([
            'command' => AddRootUserCommand::COMMAND_NAME
        ]);
    }

    /**
     * @covers \App\Command\AddRootUserCommand::execute
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
