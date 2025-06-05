<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\InvalidateUserTokenCommand;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;
use DateTime;

/**
 * Class InvalidateUserTokenCommandTest
 */
#[Group('cli')]
final class InvalidateUserTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected m\MockInterface $authenticationRepository;
    protected CommandTester $commandTester;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->authenticationRepository = m::mock(AuthenticationRepository::class);

        $command = new InvalidateUserTokenCommand($this->userRepository, $this->authenticationRepository);
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
        unset($this->authenticationRepository);
        unset($this->commandTester);
    }

    public function testHappyPathExecute(): void
    {
        $now = new DateTime();
        sleep(2);
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setInvalidateTokenIssuedBefore');
        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getAuthentication')->andReturn($authentication);
        $user->shouldReceive('getFirstAndLastName')->andReturn('somebody great');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
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

    public function testNoAuthenticationForUser(): void
    {
        $authentication = m::mock(AuthenticationInterface::class);
        $authentication->shouldReceive('setUser');
        $authentication->shouldReceive('setInvalidateTokenIssuedBefore');

        $user = m::mock(UserInterface::class);
        $user->shouldReceive('getAuthentication')->andReturn(null);
        $user->shouldReceive('getFirstAndLastName')->andReturn('somebody great');

        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->authenticationRepository->shouldReceive('create')->andReturn($authentication);
        $this->authenticationRepository->shouldReceive('update')->with($authentication);
        $this->commandTester->execute([
            'userId' => '1',
        ]);

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/All the tokens for somebody great issued before Today at [0-9:APM\s]+ UTC have been invalidated./',
            $output
        );
    }

    public function testBadUserId(): void
    {
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn(null);
        $this->expectException(Exception::class);
        $this->commandTester->execute([
            'userId' => '1',
        ]);
    }

    public function testUserRequired(): void
    {
        $this->expectException(RuntimeException::class);
        $this->commandTester->execute([]);
    }
}
