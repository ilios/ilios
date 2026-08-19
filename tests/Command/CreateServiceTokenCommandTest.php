<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Service\SecretManager;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\ServiceTokenUser;
use App\Command\CreateServiceTokenCommand;
use App\Entity\ServiceToken;
use App\Repository\ServiceTokenRepository;
use App\Service\ServiceTokenUserProvider;
use DateInterval;
use Mockery\Adapter\Phpunit\MockeryPHPUnitIntegration;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Tester\CommandTester;
use Mockery as m;

/**
 * @package App\Tests\Command
 */
#[Group('cli')]
#[CoversClass(CreateServiceTokenCommand::class)]
final class CreateServiceTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected TokenFactory $tokenFactory;
    protected TokenCodec $tokenCodec;
    protected CommandTester $commandTester;
    protected m\MockInterface $tokenProvider;
    protected m\MockInterface $serviceTokenRepository;


    public function setUp(): void
    {
        parent::setUp();
        $this->tokenProvider = m::mock(ServiceTokenUserProvider::class);
        $this->serviceTokenRepository = m::mock(ServiceTokenRepository::class);
        $this->tokenCodec = new TokenCodec(
            new SecretManager(
                'FFFFFFFFFFFFBBBBBBBBBBAAAAAAAADDDD',
                'SDFSFSSSSSSSSSSSSSSDDDDDDDDWED'
            )
        );
        $this->tokenFactory = new TokenFactory();
        $command = new CreateServiceTokenCommand(
            $this->serviceTokenRepository,
            $this->tokenProvider,
            $this->tokenFactory,
            $this->tokenCodec
        );
        $kernel = self::bootKernel();
        $application = new Application($kernel);
        $application->addCommands([$command]);
        $commandInApp = $application->find($command->getName());
        $this->commandTester = new CommandTester($commandInApp);
    }

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->commandTester);
        unset($this->serviceTokenRepository);
        unset($this->tokenProvider);
        unset($this->tokenCodec);
        unset($this->tokenFactory);
    }

    public function testNewDefaultToken(): void
    {
        $serviceTokenId = 10;
        $ttl = 'P30D';
        $description = 'lorem ipsum';
        $serviceToken = new ServiceToken();
        $serviceToken->setId($serviceTokenId);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);

        $this->commandTester->execute([
            'ttl' => $ttl,
            'description' => $description,
        ]);

        $this->assertEquals($description, $serviceToken->getDescription());
        $this->assertTrue($serviceToken->isEnabled());
        $this->assertSame(
            $serviceToken->getCreatedAt()->add(new DateInterval($ttl))->getTimestamp(),
            $serviceToken->getExpiresAt()->getTimestamp()
        );

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);

        $this->assertCount(7, $data);
        $this->assertSame(
            DateTimeImmutable::createFromFormat('U', $data['iat'])->add(new DateInterval($ttl))->getTimestamp(),
            DateTimeImmutable::createFromFormat('U', $data['exp'])->getTimestamp()
        );
        $this->assertSame(TokenManager::TOKEN_DEFAULT_AUDIENCE, $data['aud']);
        $this->assertSame(TokenManager::TOKEN_DEFAULT_ISSUER, $data['iss']);
        $this->assertSame($serviceTokenId, $data['token_id']);
        $this->assertSame([], $data['writeable_schools']);
        $this->assertFalse($data['can_generate_user_tokens']);
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

    #[DataProvider('createTokenWithWriteableSchoolsProvider')]
    public function testCreateTokenWithWriteableSchools(
        string $schoolIdsInput,
        array $expectedSchoolIdsInToken
    ): void {
        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);

        $this->commandTester->execute([
            'ttl' => 'PT30M',
            'description' => 'lorem ipsum',
            '--writeable-schools' => $schoolIdsInput,
        ]);

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);

        $this->assertSame($expectedSchoolIdsInToken, $data['writeable_schools']);
    }

    public static function createServiceTokenToCreateUserTokensProvider(): array
    {
        return [
            [true],
            [false],
        ];
    }

    #[DataProvider('createServiceTokenToCreateUserTokensProvider')]
    public function testCreateServiceTokenToCreateUserTokens(bool $allowUserTokenGeneration): void
    {
        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);

        $this->commandTester->execute([
            'ttl' => 'PT30M',
            'description' => 'lorem ipsum',
           '--allow-user-token-generation' => $allowUserTokenGeneration,
        ]);

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);

        $this->assertSame($allowUserTokenGeneration, $data['can_generate_user_tokens']);
    }
    public static function createServiceTokenWithLtiDashboardClaimProvider(): array
    {
        return [
            [true, 'lti-dashboard'],
            [false, 'ilios'],
        ];
    }

    #[DataProvider('createServiceTokenWithLtiDashboardClaimProvider')]
    public function testCreateServiceTokenWithLtiDashboardClaim(bool $hasClaim, string $expectedAud): void
    {
        $serviceToken = new ServiceToken();
        $serviceToken->setId(1);
        $this->serviceTokenRepository->shouldReceive('create')
            ->andReturn($serviceToken);
        $this->serviceTokenRepository->shouldReceive('update')->with($serviceToken);
        $this->tokenProvider->shouldReceive('loadUserByIdentifier')->andReturn(
            new ServiceTokenUser($serviceToken)
        );

        $this->commandTester->execute([
            'ttl' => 'P30D',
            'description' => 'lorem ipsum',
            '--grant-lti-dashboard-audience-claim' => $hasClaim,
        ]);

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);

        $this->assertSame($expectedAud, $data['aud']);
    }

    public function testDescriptionRequired(): void
    {
        $this->expectExceptionMessage('Not enough arguments (missing: "description").');
        $this->commandTester->execute([
            'ttl' => 'P30D',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
    }

    public function testTtlRequired(): void
    {
        $this->expectExceptionMessage('Not enough arguments (missing: "ttl").');
        $this->commandTester->execute([
            'description' => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
    }

    public function testTtlExceedsAllowedMaximum(): void
    {
        $this->commandTester->execute([
            'ttl' => 'P1000D', // one.thousand.days.
            'description' => 'lorem ipsum',
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
            'ttl' => 'nyet',
            'description' => 'lorem ipsum',
        ]);
        $this->assertEquals(Command::INVALID, $this->commandTester->getStatusCode());
        $this->assertStringStartsWith(
            'Unable to parse given TTL value.',
            $this->commandTester->getDisplay()
        );
    }

    protected function getJwtFromOutput(string $output): string
    {
        $re = '/Token (.*)/';
        preg_match($re, $output, $matches);
        return $matches[1];
    }
}
