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
     * @covers Ilios\CoreBundle\Entity\SessionType::getSessionTypeId
     */
    public function testGetSessionTypeId()
    {
        $this->basicGetTest('sessionTypeId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setSessionTypeCssClass
     */
    public function testSetSessionTypeCssClass()
    {
        $this->basicSetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getSessionTypeCssClass
     */
    public function testGetSessionTypeCssClass()
    {
        $this->basicGetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessment
     */
    public function testSetAssessment()
    {
        $this->basicSetTest('assessment', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getAssessment
     */
    public function testGetAssessment()
    {
        $this->basicGetTest('assessment', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setAssessmentOption
     */
    public function testSetAssessmentOption()
    {
        $this->entitySetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getAssessmentOption
     */
    public function testGetAssessmentOption()
    {
        $this->entityGetTest('assessmentOption', 'AssessmentOption');
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

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getAamcMethods
     */
    public function testGetAamcMethods()
    {
        $this->entityCollectionGetTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\SessionType::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->entityGetTest('owningSchool', 'School');
    }
}
