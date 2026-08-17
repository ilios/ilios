<?php

declare(strict_types=1);

namespace App\Tests\Command;

use App\Classes\SessionUserInterface;
use App\Service\SecretManager;
use App\Service\SessionUserPermissionChecker;
use App\Service\SessionUserProvider;
use App\Service\TokenCodec;
use App\Service\TokenFactory;
use App\Service\TokenManager;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Group;
use App\Command\CreateUserTokenCommand;
use App\Entity\UserInterface;
use App\Repository\UserRepository;
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
#[CoversClass(CreateUserTokenCommand::class)]
final class CreateUserTokenCommandTest extends KernelTestCase
{
    use MockeryPHPUnitIntegration;

    protected m\MockInterface $userRepository;

    protected m\MockInterface $sessionUserProvider;

    protected m\MockInterface $sessionUserPermissionChecker;


    protected CommandTester $commandTester;
    protected TokenCodec $tokenCodec;

    protected TokenManager $tokenManager;


    public function setUp(): void
    {
        parent::setUp();
        $this->userRepository = m::mock(UserRepository::class);
        $this->sessionUserPermissionChecker = m::mock(SessionUserPermissionChecker::class);
        $this->tokenCodec = new TokenCodec(new SecretManager(
            'FFFFFFFFFFFAAAAAAAADDDDDDDDDDDDDDDDDFFFFFFFFFF',
            'GGGGGRRRRRRRRRRRRRSSSSSSSSSAAAAAAAA'
        ));
        $this->tokenManager = new TokenManager(
            $this->tokenCodec,
            new TokenFactory(),
            $this->sessionUserPermissionChecker,
        );
        $this->sessionUserProvider = m::mock(SessionUserProvider::class);
        $command = new CreateUserTokenCommand(
            $this->userRepository,
            $this->tokenManager,
            $this->tokenCodec,
            $this->sessionUserProvider,
        );
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
        unset($this->tokenManager);
        unset($this->tokenCodec);
        unset($this->sessionUserProvider);
        unset($this->sessionUserPermissionChecker);
        unset($this->commandTester);
    }

    #[DataProvider('newDefaultTokenProvider')]
    public function testNewDefaultToken(
        int $userId,
        bool $isRoot,
        bool $performsNonLearnerFunction,
        bool $canCreateOrUpdateUserInAnySchool,
    ): void {
        $user = m::mock(UserInterface::class)->shouldReceive('getId')->andReturn($userId)->getMock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => $userId])->andReturn($user);
        $sessionUserMock = m::mock(SessionUserInterface::class);
        $sessionUserMock->shouldReceive('getId')->andReturn($userId);
        $sessionUserMock->shouldReceive('performsNonLearnerFunction')->andReturn($performsNonLearnerFunction);
        $sessionUserMock->shouldReceive('isRoot')->andReturn($isRoot);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUserMock)
            ->andReturn($canCreateOrUpdateUserInAnySchool);
        $this->sessionUserProvider
            ->shouldReceive('createSessionUserFromUserId')
            ->with($userId)
            ->andReturn($sessionUserMock);

        $this->commandTester->execute([
            'userId' => (string) $userId,
        ]);

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);
        $this->assertCount(10, $data);
        $iat = DateTimeImmutable::createFromFormat('U', $data['iat']);
        $exp = DateTimeImmutable::createFromFormat('U', $data['exp']);
        $firstCreatedAt = DateTimeImmutable::createFromFormat('U', $data['firstCreatedAt']);
        $this->assertSame(
            $exp->getTimestamp(),
            $iat->add(new DateInterval('PT8H'))->getTimestamp()
        );
        $this->assertSame($userId, $data['user_id']);
        $this->assertSame('ilios', $data['iss']);
        $this->assertSame(['ilios'], $data['aud']);
        $this->assertSame($firstCreatedAt->getTimestamp(), $iat->getTimestamp());
        $this->assertSame($isRoot, $data['is_root']);
        $this->assertSame($performsNonLearnerFunction, $data['performs_non_learner_function']);
        $this->assertSame($canCreateOrUpdateUserInAnySchool, $data['can_create_or_update_user_in_any_school']);
        $this->assertSame(0, $data['refreshCount']);
    }

    public static function newDefaultTokenProvider(): array
    {
        return [
            [10, false, true, false],
            [20, true, false, true],
        ];
    }

    public function testNewTTLToken(): void
    {
        $ttl = 'P7D';
        $user = m::mock(UserInterface::class)->shouldReceive('getId')->andReturn(1)->getMock();
        $this->userRepository->shouldReceive('findOneBy')->with(['id' => 1])->andReturn($user);
        $sessionUserMock = m::mock(SessionUserInterface::class);
        $sessionUserMock->shouldReceive('getId')->andReturn(1);
        $sessionUserMock->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        $sessionUserMock->shouldReceive('isRoot')->andReturn(false);
        $this->sessionUserPermissionChecker
            ->shouldReceive('canCreateOrUpdateUsersInAnySchool')
            ->with($sessionUserMock)
            ->andReturn(true);
        $this->sessionUserProvider
            ->shouldReceive('createSessionUserFromUserId')
            ->with(1)
            ->andReturn($sessionUserMock);

        $this->commandTester->execute([
            'userId' => '1',
            '--ttl' => $ttl,
        ]);

        $output = $this->commandTester->getDisplay();
        $jwt = $this->getJwtFromOutput($output);
        $data = $this->tokenCodec->decode($jwt);
        $iat = DateTimeImmutable::createFromFormat('U', $data['iat']);
        $exp = DateTimeImmutable::createFromFormat('U', $data['exp']);
        $this->assertSame($iat->add(new DateInterval($ttl))->getTimestamp(), $exp->getTimestamp());
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

    protected function getJwtFromOutput(string $output): string
    {
        $re = '/Token (.*)/';
        preg_match($re, $output, $matches);
        return $matches[1];
    }
}
