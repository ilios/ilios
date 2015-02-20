<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\SessionType;
use Mockery as m;

/**
 * Tests for Entity SessionType
 */
class SessionTypeTest extends EntityBase
{
    /**
     * @var SessionType
     */
    protected $object;

    /**
     * Instantiate a SessionType object
     */
    protected function setUp()
    {
        $this->object = new SessionType;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcMethods());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setSessionTypeCssClass
     */
    public function testSetSessionTypeCssClass()
    {
        $this->basicSetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessment
     */
    public function testIsAssessment()
    {
        $this->booleanSetTest('assessment');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessmentOption
     */
    public function testSetAssessmentOption()
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::addAamcMethod
     */
    public function testAddAamcMethod()
    {
        $this->entityCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::removeAamcMethod
     */
    public function testRemoveAamcMethod()
    {
        $this->entityCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }
}
