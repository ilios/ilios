<?php
namespace Tests\AuthenticationBundle\RelationshipVoter;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\VoterInterface;

class AbstractBase extends TestCase
{
    use m\Adapter\Phpunit\MockeryPHPUnitIntegration;

    /** @var  m\MockInterface */
    protected $permissionChecker;

    /** @var  VoterInterface */
    protected $voter;

    /**
     * Remove all mock objects
     */
    public function tearDown()
    {
        unset($this->voter);
        unset($this->permissionChecker);
    }

    /**
     * Creates a mock token that has the given user.
     * @param SessionUserInterface $sessionUser A (mock) user entity.
     * @return TokenInterface
     */
    protected function createMockTokenWithSessionUser(SessionUserInterface $sessionUser = null)
    {
        $mock = m::mock(TokenInterface::class);
        $mock->shouldReceive('getUser')->andReturn($sessionUser);
        return $mock;
    }

    /**
     * Creates a mock token with a non-root user
     * @return TokenInterface
     */
    protected function createMockTokenWithNonRootSessionUser()
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(false);
        return $this->createMockTokenWithSessionUser($sessionUser);
    }

    protected function checkRootAccess($entityClass, $dtoClass)
    {
        $sessionUser = m::mock(SessionUserInterface::class);
        $sessionUser->shouldReceive('isRoot')->andReturn(true);
        $token = $this->createMockTokenWithSessionUser($sessionUser);
        foreach ([AbstractVoter::VIEW, AbstractVoter::DELETE, AbstractVoter::CREATE, AbstractVoter::EDIT] as $attr) {
            $response = $this->voter->vote($token, m::mock($entityClass), [$attr]);
            $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "${attr} allowed");
        }
        $response = $this->voter->vote($token, m::mock($dtoClass), [AbstractVoter::VIEW]);
        $this->assertEquals(VoterInterface::ACCESS_GRANTED, $response, "DTO View allowed");
    }
}
