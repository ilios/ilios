<?php

namespace Ilios\AuthenticationBundle\Tests\Voter;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\LearningMaterial;
use Ilios\CoreBundle\Entity\User;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Entity\UserRole;
use Mockery as m;

/**
 * Class LearningMaterialVoterTest
 * @package Ilios\AuthenticationBundle\Tests\Voter
 */
class LearningMaterialVoterTest extends \PHPUnit_Framework_TestCase
{
    protected $voter;

    /**
     * @inheritdoc
     */
    public function setUp()
    {
        $this->voter = m::mock('Ilios\AuthenticationBundle\Voter\LearningMaterialVoter')
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    /**
     * @inheritdoc
     */
    public function tearDown()
    {
        m::close();
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsGrantedWithNoUser()
    {
        $this->assertFalse($this->voter->isGranted(AbstractVoter::VIEW, new LearningMaterial(), null));
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     * @dataProvider testIsViewGrantedAllowedProvider
     */
    public function testIsViewGrantedAllowed(UserInterface $user)
    {
        $this->assertTrue($this->voter->isGranted(AbstractVoter::VIEW, new LearningMaterial(), $user));
    }

    /**
     * @return array
     */
    public function testIsViewGrantedAllowedProvider()
    {
        return [
            [$this->createUser()],
            [$this->createUser(['Student'])],
            [$this->createUser(['Course Director'])],
            [$this->createUser(['Faculty', 'Developer'])],
            [$this->createUser(['Any arbitrary role'])],
        ];
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsDeleteGrantedAllowed()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsDeleteGrantedDenied()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     * @dataProvider testIsCreateGrantedAllowedProvider
     */
    public function testIsCreateGrantedAllowed(UserInterface $user)
    {
        $this->assertTrue($this->voter->isGranted(AbstractVoter::CREATE, new LearningMaterial(), $user));
    }

    /**
     * @return array
     */
    public function testIsCreateGrantedAllowedProvider()
    {
        return [
            [$this->createUser(['Course Director'])],
            [$this->createUser(['Developer'])],
            [$this->createUser(['Faculty'])],
        ];
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     * @dataProvider testIsCreateGrantedDeniedProvider
     */
    public function testIsCreateGrantedDenied(UserInterface $user)
    {
        $this->assertFalse($this->voter->isGranted(AbstractVoter::CREATE, new LearningMaterial(), $user));
    }

    /**
     * @return array
     */
    public function testIsCreateGrantedDeniedProvider()
    {
        return [
            [$this->createUser()],
            [$this->createUser(['Student'])],
            [$this->createUser(['Public'])],
            [$this->createUser(['Former Student'])],
        ];
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsUpdateGrantedAllowed()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    public function testIsUpdateGrantedAllowedProvider()
    {
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsUpdateGrantedDenied()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @param array $rolesTitles
     * @return UserInterface
     */
    protected function createUser(array $rolesTitles = array())
    {
        $user = new User();
        foreach ($rolesTitles as $title) {
            $role = new UserRole();
            $role->setTitle($title);
            $user->addRole($role);
        }
        return $user;
    }
}