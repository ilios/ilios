<?php

declare(strict_types=1);

namespace App\Tests\ServiceTokenVoter;

use App\Classes\ServiceToken;
use App\Classes\SessionUserInterface;
use App\Classes\UserToken;
use DateInterval;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Classes\ServiceTokenUserInterface;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\User\UserInterface;

abstract class AbstractBase extends TestCase
{
    final protected Voter $voter;

    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->voter);
    }

    abstract public static function supportsTypeProvider(): array;

    #[DataProvider('supportsTypeProvider')]
    public function testSupportType(string $className, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsType($className), $isSupported);
    }

    abstract public static function supportsAttributesProvider(): array;

    #[DataProvider('supportsAttributesProvider')]
    public function testSupportAttributes(string $attribute, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsAttribute($attribute), $isSupported);
    }

    protected function createMockSecurityToken(
        ?UserInterface $tokenUser,
        UserToken|ServiceToken|null $token = null,
    ): TokenInterface {
        $mockSecurityToken = m::mock(TokenInterface::class);
        $mockSecurityToken->shouldReceive('getUser')->andReturn($tokenUser);
        $mockSecurityToken->shouldReceive('getAttribute')->with('token')->andReturn($token);
        return $mockSecurityToken;
    }

    protected function createMockTokenWithServiceTokenUser(array $writeableSchoolIds = []): TokenInterface
    {
        $serviceToken = new ServiceToken(
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('P1D')),
            ['doesntmatter'],
            'whocaresnotme',
            1,
            $writeableSchoolIds,
            false,
        );

        return $this->createMockSecurityToken(
            m::mock(ServiceTokenUserInterface::class),
            $serviceToken
        );
    }

    protected function createMockTokenWithoutServiceTokenUser(): TokenInterface
    {
        return $this->createMockSecurityToken(null);
    }

    protected function createMockTokenWithSessionUser(): TokenInterface
    {
        $userToken = new UserToken(
            new DateTimeImmutable(),
            new DateTimeImmutable()->add(new DateInterval('P1D')),
            ['doesntmatter'],
            'whocaresnotme',
            1,
            true,
            true,
            true,
            null,
            new DateTimeImmutable(),
            0
        );

        return $this->createMockSecurityToken(
            m::mock(SessionUserInterface::class),
            $userToken
        );
    }
}
