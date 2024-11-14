<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\ServiceTokenUser;
use App\Command\CreateServiceTokenCommand;
use App\Entity\ServiceToken;
use App\Entity\ServiceTokenInterface;
use App\Repository\ServiceTokenRepository;
use App\Service\JsonWebTokenManager;
use App\Service\ServiceTokenUserProvider;
use DateInterval;
use DateTime;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 */
#[\PHPUnit\Framework\Attributes\Group('cli')]
#[\PHPUnit\Framework\Attributes\CoversClass(\App\Command\CreateServiceTokenCommand::class)]
class CreateServiceTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected CommandTester $commandTester;
    protected m\MockInterface $jwtManager;
    protected m\MockInterface $tokenProvider;
    protected m\MockInterface $serviceTokenRepository;

    public function setUp(): void
    {
        parent::setUp();
        $this->jwtManager = m::mock(JsonWebTokenManager::class);
        $this->tokenProvider = m::mock(ServiceTokenUserProvider::class);
        $this->serviceTokenRepository = m::mock(ServiceTokenRepository::class);
        $command = new CreateServiceTokenCommand(
            $this->serviceTokenRepository,
            $this->tokenProvider,
            $this->jwtManager
        );
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
        unset($this->commandTester);
        unset($this->serviceTokenRepository);
        unset($this->tokenProvider);
        unset($this->jwtManager);
    }

    public function testNewDefaultToken(): void
    {
        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);
        $this->tokenProvider->shouldReceive('loadUserByIdentifier')->andReturn(
            new ServiceTokenUser($serviceToken)
        );
        $this->jwtManager
            ->shouldReceive('createJwtFromServiceTokenUser')
            ->withArgs(function (ServiceTokenUser $tokenUser, array $schoolIds) {
                $this->assertEquals(1, $tokenUser->getId());
                $this->assertEquals(
                    $tokenUser
                        ->getCreatedAt()
                        ->add(new DateInterval(CreateServiceTokenCommand::TTL_MAX_VALUE))
                        ->getTimestamp(),
                    $tokenUser->getExpiresAt()->getTimestamp(),
                );
                $this->assertEquals([], $schoolIds);
                return true;
            })->andReturn('abcde');

        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => CreateServiceTokenCommand::TTL_MAX_VALUE,
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
        ]);
        $this->assertEquals('lorem ipsum', $serviceToken->getDescription());

        $output = $this->commandTester->getDisplay();
        $this->assertMatchesRegularExpression(
            '/Token abcde/',
            $output
        );
    }

    public static function createTokenWithWriteableSchoolsProvider(): array
    {
        return [
            ['1', [1]],
            ['1, 2, 4', [1, 2, 4]],
            ['1,2,1,2,4', [1, 2, 4]],
            ['a, b, 1, d, 4', [1, 4]],
        ];
    }

    #[\PHPUnit\Framework\Attributes\DataProvider('createTokenWithWriteableSchoolsProvider')]
    public function testCreateTokenWithWriteableSchools(
        string $schoolIdsInput,
        array $expectedSchoolIdsInToken
    ): void {
        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);
        $this->tokenProvider->shouldReceive('loadUserByIdentifier')->andReturn(
            new ServiceTokenUser($serviceToken)
        );
        $this->jwtManager
            ->shouldReceive('createJwtFromServiceTokenUser')
            ->withArgs(function (ServiceTokenUser $tokenUser, array $schoolIds) use ($expectedSchoolIdsInToken) {
                $this->assertEquals(1, $tokenUser->getId());
                $this->assertEquals(
                    $tokenUser
                        ->getCreatedAt()
                        ->add(new DateInterval(CreateServiceTokenCommand::TTL_MAX_VALUE))
                        ->getTimestamp(),
                    $tokenUser->getExpiresAt()->getTimestamp(),
                );
                $this->assertEquals($schoolIds, $expectedSchoolIdsInToken);
                return true;
            })->andReturn('abcde');

        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => CreateServiceTokenCommand::TTL_MAX_VALUE,
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
            '--' . CreateServiceTokenCommand::WRITEABLE_SCHOOLS_KEY => $schoolIdsInput,
            ]);
    }

    public function testCreateTokenWithCustomTtl(): void
    {
        $ttl = 'P90D';
        $this->assertNotEquals(CreateServiceTokenCommand::TTL_MAX_VALUE, $ttl);

        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);
        $this->tokenProvider->shouldReceive('loadUserByIdentifier')->andReturn(
            new ServiceTokenUser($serviceToken)
        );
        $this->jwtManager
            ->shouldReceive('createJwtFromServiceTokenUser')
            ->withArgs(function (ServiceTokenUser $tokenUser, array $schoolIds) use ($ttl) {
                $this->assertEquals(1, $tokenUser->getId());
                $this->assertEquals(
                    $tokenUser
                        ->getCreatedAt()
                        ->add(new DateInterval($ttl))
                        ->getTimestamp(),
                    $tokenUser->getExpiresAt()->getTimestamp(),
                );
                return true;
            })->andReturn('abcde');

        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => $ttl,
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::SUCCESS, $this->commandTester->getStatusCode());
    }

    public function testDescriptionRequired(): void
    {
        $this->expectExceptionMessage('Not enough arguments (missing: "description").');
        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => CreateServiceTokenCommand::TTL_MAX_VALUE,
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
    }

    public function testTtlRequired(): void
    {
        $this->expectExceptionMessage('Not enough arguments (missing: "ttl").');
        $this->commandTester->execute([
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
    }

    public function testTtlExceedsAllowedMaximum(): void
    {
        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => 'P1000D', // one.thousand.days.
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
        $this->assertStringStartsWith(
            'The given time-to-live exceeds the maximum allowed value (P180D).',
            $this->commandTester->getDisplay()
        );
    }

    public function testInvalidTtl(): void
    {
        $this->commandTester->execute([
            CreateServiceTokenCommand::TTL_KEY => 'nyet',
            CreateServiceTokenCommand::DESCRIPTION_KEY => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
        $this->assertStringStartsWith(
            'Unable to parse given TTL value.',
            $this->commandTester->getDisplay()
        );
    }
}
