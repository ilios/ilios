<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\InvalidateUserTokenCommand;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use DateTime;

/**
 * Class InvalidateUserTokenCommandTest
 * @group cli
 */
class InvalidateUserTokenCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:invalidate-user-tokens';

    protected $userRepository;
    protected $authenticationRepository;
    protected $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);

        $command = new InvalidateUserTokenCommand($this->userRepository, $this->authenticationRepository);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->add($command);
        $commandInApp = $application->find(self::COMMAND_NAME);
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
        unset($this->commandTester);
    }

    public function testHappyPathExecute()
    {
        $now = new DateTime();
        sleep(2);
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setInvalidateTokenIssuedBefore')->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn($authentication)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );

        preg_match('/[0-9:APM\s]+ UTC/', $output, $matches);
        $time = trim($matches[0]);
        $since = new DateTime($time);
        $diff = $since->getTimestamp() - $now->getTimestamp();
        $this->assertTrue(
            $diff > 1
        );
    }

    public function testNoAuthenticationForUser()
    {
        $authentication = m::mock('App\Entity\AuthenticationInterface')
            ->shouldReceive('setUser')
            ->shouldReceive('setInvalidateTokenIssuedBefore')
            ->mock();
        $user = m::mock('App\Entity\UserInterface')
            ->shouldReceive('getAuthentication')->andReturn(null)
            ->shouldReceive('getFirstAndLastName')->andReturn('somebody great')
            ->mock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->authenticationRepository
            ->shouldReceive('create')->andReturn($authentication)
            ->shouldReceive('update')->with($authentication);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertRegExp(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );
    }

    public function testBadUserId()
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(\Exception::class);
        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);
    }

    public function testUserRequired()
    {
        $this->expectException(\RuntimeException::class);
        $this->commandTester->execute(['command' => self::COMMAND_NAME]);
    }
}
