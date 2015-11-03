<?php

namespace Ilios\AuthenticationBundle\Tests\Voter;

use Ilios\AuthenticationBundle\Voter\AbstractVoter;
use Ilios\CoreBundle\Entity\LearningMaterial;
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
        // @todo implement [ST 2015/11/02]
        $this->assertFalse($this->voter->isGranted(AbstractVoter::VIEW, new LearningMaterial(), null));
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsViewGrantedAllowed()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsViewGrantedDenied()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
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
     */
    public function testIsCreateGrantedAllowed()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsCreateGrantedDenied()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
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

    /**
     * @covers Ilios\AuthenticationBundle\Voter\LearningMaterialVoter::isGranted
     */
    public function testIsupdateGrantedDenied()
    {
        // @todo implement [ST 2015/11/02]
        $this->markTestIncomplete(
            'This test has not been implemented yet.'
        );
    }
}