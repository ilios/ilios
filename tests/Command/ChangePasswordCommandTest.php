<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Command\ChangePasswordCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\SessionUserProvider;
use App\Tests\Helper\TestQuestionHelper;
use Exception;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class ChangePasswordCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class ChangePasswordCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:change-password';

    /** @var CommandTester */
    protected $commandTester;

    /** @var UserRepository */
    protected $userRepository;
    /** @var AuthenticationRepository */
    protected $authenticationRepository;
    /** @var UserPasswordEncoderInterface */
    protected $encoder;
    /** @var SessionUserProvider */
    protected $sessionUserProvider;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);
        $this->encoder = m::mock(UserPasswordEncoderInterface::class);
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);

        $command = new ChangePasswordCommand(
            $this->userRepository,
            $this->authenticationRepository,
            $this->encoder,
            $this->sessionUserProvider
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);

        // Override the question helper to fix testing issue with hidden password input
        $helper = new TestQuestionHelper();
        $commandInApp->getHelperSet()->set($helper, 'question');
        $this->commandTester = new CommandTester($commandInApp);
    }

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->userRepository);
        unset($this->authenticationRepository);
        unset($this->encoder);
        unset($this->sessionUserProvider);
        unset($this->commandTester);
    }

    public function testChangePassword()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['123456789']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn($authentication);

        $sessionUser = m::mock(SessionUserInterface::class);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, '123456789')->andReturn('abc');
        $authentication->shouldReceive('setPasswordHash')->with('abc')->once();

        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute(['command' => self::COMMAND_NAME, 'userId' => '1']);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Password Changed/',
            $output
        );
    }

    public function testUserWithoutAuthentication()
    {
        $user = m::mock(UserInterface::class);
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->commandTester->setInputs(['123456789']);

        $authentication = m::mock(AuthenticationInterface::class);
        $user->shouldReceive('getAuthentication')->once()->andReturn(null);
        $this->authenticationRepository->shouldReceive('create')->once()->andReturn($authentication);
        $user->shouldReceive('setAuthentication')->once()->with($authentication);

        $sessionUser = m::mock(SessionUserInterface::class);
        $this->sessionUserProvider->shouldReceive('createSessionUserFromUser')->with($user)->andReturn($sessionUser);

        $this->encoder->shouldReceive('encodePassword')->with($sessionUser, '123456789')->andReturn('abc');
        $authentication->shouldReceive('setPasswordHash')->with('abc')->once();

        $this->authenticationRepository->shouldReceive('update')->with($authentication);

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Password Changed/',
            $output
        );
    }

    public function testBadUserId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testUserRequired()
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
