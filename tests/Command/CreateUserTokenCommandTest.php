<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Command\CreateUserTokenCommand;
use App\Repository\UserRepository;
use App\Service\JsonWebTokenManager;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * Class CreateUserTokenCommandTest
 * @package App\Tests\Command
 * @group cli
 */
class CreateUserTokenCommandTest extends KernelTestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    private const COMMAND_NAME = 'ilios:create-user-token';

    protected $userRepository;
    protected $commandTester;

    /**
     * @var JsonWebTokenManager|m\LegacyMockInterface|m\MockInterface
     */
    protected $jwtManager;

    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->jwtManager = m::mock(JsonWebTokenManager::class);

        $command = new CreateUserTokenCommand($this->userRepository, $this->jwtManager);
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
        unset($this->commandTester);
        unset($this->jwtManager);
    }

    public function testNewDefaultToken()
    {
        $user = m::mock('App\Entity\UserInterface');
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUser')->with($user, 'PT8H')->andReturn('123JWT');

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'         => '1'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Token 123JWT/',
            $output
        );
    }

    public function testNewTTLToken()
    {
        $user = m::mock('App\Entity\UserInterface');
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $this->jwtManager->shouldReceive('createJwtFromUser')->with($user, '108Franks')->andReturn('123JWT');

        $this->commandTester->execute([
            'command'      => self::COMMAND_NAME,
            'userId'       => '1',
            '--ttl'        => '108Franks'
        ]);


        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Token 123JWT/',
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
