<?php

declare(strict_types=1);

namespace App\Tests\Command;

use PHPUnit\Framework\Attributes\Group;
use App\Command\CreateUserTokenCommand;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
use App\Service\JsonWebTokenManager;
use Exception;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class CreateUserTokenCommandTest
 * @package App\Tests\Command
 */
#[Group('cli')]
final class CreateUserTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;
    protected CommandTester $commandTester;
    protected m\MockInterface $jwtManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);

        $command = new CreateUserTokenCommand($this->userRepository, $this->jwtManager);
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
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
        unset($this->commandTester);
        unset($this->jwtManager);
    }

    public function testNewDefaultToken(): void
    {
        $user = m::mock(UserInterface::class)->shouldReceive('getId')->andReturn(1)->getMock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUserId')->with(1, 'PT8H')->andReturn('123JWT');

        $this->commandTester->execute([
            'userId' => '1',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Token 123JWT/',
            $output
        );
    }

    public function testNewTTLToken(): void
    {
        $user = m::mock(UserInterface::class)->shouldReceive('getId')->andReturn(1)->getMock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUserId')->with(1, '108Franks')->andReturn('123JWT');
        $this->commandTester->execute([
            'userId' => '1',
            '--ttl' => '108Franks',
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Token 123JWT/',
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
