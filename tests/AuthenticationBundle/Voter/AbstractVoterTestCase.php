<?php

namespace Tests\AuthenticationBundle\Voter;

use Doctrine\Common\Collections\ArrayCollection;
use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\UserRoleInterface;
use Mockery as m;

/**
 * Class AbstractVoterTestCase
 * @package Tests\AuthenticationBundle\\Voter
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
        $mock = m::mock('Ilios\CoreBundle\Entity\UserRole');
        $mock->shouldReceive('getTitle')->andReturn($title);
        return $mock;
    }

    /**
     * Creates a mock session user that has the given user roles.
     *
     * Have to mock two hasRole methods in order to check the arguments.  If
     * this is a role we should have then the first gets called and TRUE is returned
     * otherwise with second shouldReceive returns false
     * 
     * @param array $roles A list of (mock) user-role entities.
     * @return \Mockery\Mock
     */
    protected function createMockSessionUserWithUserRoles(array $roles)
    {
        $mock = m::mock('Ilios\AuthenticationBundle\Classes\SessionUserInterface');
        $mock->shouldReceive('hasRole')->with(\Mockery::on(function($wantedRoles) use ($roles) {
            $roleTitles = array_map(function(UserRoleInterface $role) {
                return $role->getTitle();
            }, $roles);
            $intersection = array_intersect($wantedRoles, $roleTitles);

            return ! empty($intersection);
        }))->andReturn(true);
        $mock->shouldReceive('hasRole')->andReturn(false);

        return $mock;
    }

    /**
     * Creates a mock token that has the given user.
     * @param SessionUserInterface $sessionUser A (mock) user entity.
     * @return \Mockery\Mock
     */
    protected function createMockTokenWithSessionUser(SessionUserInterface $sessionUser = null)
    {
        $mock = m::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $mock->shouldReceive('getUser')->andReturn($sessionUser);
        return $mock;
    }
}
