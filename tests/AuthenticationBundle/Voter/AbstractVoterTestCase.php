<?php

namespace Tests\AuthenticationBundle\Voter;

use Doctrine\Common\Collections\ArrayCollection;
use Mockery as m;

/**
 * Class AbstractVoterTestCase
 * @package Ilios\AuthenticationBundle\Tests\Voter
 */
abstract class AbstractVoterTestCase extends \PHPUnit_Framework_TestCase
{
    /**
     * @inheritdoc
     */
    protected function tearDown()
    {
        m::close();
    }

    /**
     * Creates a mock user-role entity that has the given title.
     * @param string $title A user-role title.
     * @return \Mockery\Mock
     */
    protected function createMockUserRole($title)
    {
        $mock = m::mock('Ilios\CoreBundle\Entity\UserRole')->makePartial();
        $mock->shouldReceive('getTitle')->andReturn($title);
        return $mock;
    }

    /**
     * Creates a mock user entity that has the given user roles.
     * @param array $roles A list of (mock) user-role entities.
     * @return \Mockery\Mock
     */
    protected function createMockUserWithUserRoles(array $roles)
    {
        $mock = m::mock('Ilios\CoreBundle\Entity\User')->makePartial();
        $mock->shouldReceive('getRoles')->withNoArgs()->andReturn(new ArrayCollection($roles));
        return $mock;
    }

    /**
     * Creates a mock token that has the given user.
     * @param mixed $user A (mock) user entity.
     * @return \Mockery\Mock
     */
    protected function createMockTokenWithUser($user)
    {
        $mock = m::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface')->makePartial();
        $mock->shouldReceive('getUser')->andReturn($user);
        return $mock;
    }
}
