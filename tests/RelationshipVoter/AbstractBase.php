<?php

declare(strict_types=1);

namespace App\Tests\RelationshipVoter;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

abstract class AbstractBase extends TestCase
{
    /** @var  m\MockInterface */
    protected $permissionChecker;

    protected Voter $voter;

    /**
     * Remove all mock objects
     */
    public function tearDown(): void
    {
        parent::tearDown();
        unset($this->voter);
        unset($this->permissionChecker);
    }

    /**
     * Creates a mock token that has the given user.
     * @param ?SessionUserInterface $sessionUser A (mock) user entity.
     */
    protected function createMockTokenWithSessionUser(?SessionUserInterface $sessionUser): TokenInterface
    {
        $mock = m::mock(TokenInterface::class);
        $mock->shouldReceive('getUser')->andReturn($sessionUser);
        return $mock;
    }

    /**
     * Creates a mock token with a non-root user
     */
    protected function createMockTokenWithNonRootSessionUser(): TokenInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        return $this->createMockTokenWithSessionUser($sessionUser);
    }

    /**
     * Creates a mock token with a user that's performs non-learner functions.
     */
    protected function createMockTokenWithSessionUserPerformingNonLearnerFunction(): TokenInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        return $this->createMockTokenWithSessionUser($sessionUser);
    }

    /**
     * Creates a mock token with a user that's doesn't perform non-learner functions.
     */
    protected function createMockTokenWithSessionUserPerformingOnlyLearnerFunction(): TokenInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        return $this->createMockTokenWithSessionUser($sessionUser);
    }

    /**
     * Creates a mock token with a root user
     */
    protected function createMockTokenWithRootSessionUser(): TokenInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        return $this->createMockTokenWithSessionUser($sessionUser);
    }

    /**
     * Check that "root" users are granted access in all votes on the given entity.
     * @param m\MockInterface $mockEntity
     * @param array $entityAttrs
     */
    protected function checkRootEntityAccess(
        $mockEntity,
        array $entityAttrs = [
            VoterPermissions::VIEW,
            VoterPermissions::DELETE,
            VoterPermissions::CREATE,
            VoterPermissions::EDIT,
            ]
    ) {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        foreach ($entityAttrs as $attr) {
            $response = $this->voter->vote($token, $mockEntity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "{$attr} allowed");
        }
    }

    /**
     * Check that "root" users are granted access in all votes on the given DTO.
     * @param string $dtoClass
     */
    protected function checkRootDTOAccess(string $dtoClass)
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        $response = $this->voter->vote($token, m::mock($dtoClass), [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    abstract public static function supportsTypeProvider(): array;

    /**
     * @dataProvider supportsTypeProvider
     */
    public function testSupportType(string $className, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsType($className), $isSupported);
    }

    abstract public static function supportsAttributesProvider(): array;

    /**
     * @dataProvider supportsAttributesProvider
     */
    public function testSupportAttributes(string $attribute, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsAttribute($attribute), $isSupported);
    }
}
