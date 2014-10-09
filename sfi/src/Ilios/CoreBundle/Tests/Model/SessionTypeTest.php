<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\SessionType;
use Mockery as m;

/**
 * Tests for Model SessionType
 */
class SessionTypeTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\SessionType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getAamcMethods());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getSessionTypeId
     */
    public function testGetSessionTypeId()
    {
        $this->basicGetTest('sessionTypeId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::setSessionTypeCssClass
     */
    public function testSetSessionTypeCssClass()
    {
        $this->basicSetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getSessionTypeCssClass
     */
    public function testGetSessionTypeCssClass()
    {
        $this->basicGetTest('sessionTypeCssClass', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::setAssessment
     */
    public function testSetAssessment()
    {
        $this->basicSetTest('assessment', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getAssessment
     */
    public function testGetAssessment()
    {
        $this->basicGetTest('assessment', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::setAssessmentOption
     */
    public function testSetAssessmentOption()
    {
        $this->modelSetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getAssessmentOption
     */
    public function testGetAssessmentOption()
    {
        $this->modelGetTest('assessmentOption', 'AssessmentOption');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::addAamcMethod
     */
    public function testAddAamcMethod()
    {
        $this->modelCollectionAddTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::removeAamcMethod
     */
    public function testRemoveAamcMethod()
    {
        $this->modelCollectionRemoveTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getAamcMethods
     */
    public function testGetAamcMethods()
    {
        $this->modelCollectionGetTest('aamcMethod', 'AamcMethod');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->modelSetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionType::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->modelGetTest('owningSchool', 'School');
    }
}
