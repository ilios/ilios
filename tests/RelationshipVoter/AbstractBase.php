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
    protected m\MockInterface $permissionChecker;
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
     * Creates a mock token that has the given (mock) session-user.
     * @param ?m\MockInterface $mockSessionUser A mock session user.
     * @return m\MockInterface a mock service token object
     */
    protected function createMockTokenWithMockSessionUser(?m\MockInterface $mockSessionUser): m\MockInterface
    {
        $mock = m::mock(TokenInterface::class);
        $mock->shouldReceive('getUser')->andReturn($mockSessionUser);
        return $mock;
    }

    /**
     * Creates a mock session user that doesn't have root level privileges.
     */
    protected function createMockNonRootSessionUser(): m\MockInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        return $sessionUser;
    }

    /**
     * Creates a mock session user that performs non-learner functions.
     */
    protected function createMockSessionUserPerformingNonLearnerFunction(): m\MockInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(true);
        return $sessionUser;
    }

    /**
     * Creates a mock session user that doesn't perform non-learner functions.
     */
    protected function createMockSessionUserPerformingOnlyLearnerFunction(): m\MockInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        $sessionUser->shouldReceive('performsNonLearnerFunction')->andReturn(false);
        return $sessionUser;
    }

    /**
     * Creates a mock session user with root-level privileges.
     */
    protected function createMockRootSessionUser(): m\MockInterface
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        return $sessionUser;
    }

    /**
     * Check that "root" users are granted access in all votes on the given entity.
     */
    protected function checkRootEntityAccess(
        m\MockInterface $mockEntity,
        array $entityAttrs = [
            VoterPermissions::VIEW,
            VoterPermissions::DELETE,
            VoterPermissions::CREATE,
            VoterPermissions::EDIT,
            ]
    ): void {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        foreach ($entityAttrs as $attr) {
            $response = $this->voter->vote($token, $mockEntity, [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "{$attr} allowed");
        }
    }

    /**
     * Check that "root" users are granted access in all votes on the given DTO.
     */
    protected function checkRootDTOAccess(string $dtoClass): void
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithMockSessionUser($sessionUser);
        $response = $this->voter->vote($token, m::mock($dtoClass), [VoterPermissions::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }

    abstract public static function supportsTypeProvider(): array;

    #[\PHPUnit\Framework\Attributes\DataProvider('supportsTypeProvider')]
    public function testSupportType(string $className, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsType($className), $isSupported);
    }

    abstract public static function supportsAttributesProvider(): array;

    #[\PHPUnit\Framework\Attributes\DataProvider('supportsAttributesProvider')]
    public function testSupportAttributes(string $attribute, bool $isSupported): void
    {
        $this->assertEquals($this->voter->supportsAttribute($attribute), $isSupported);
    }
}
