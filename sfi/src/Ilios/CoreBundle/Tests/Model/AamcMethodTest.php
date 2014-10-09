<?php
namespace Ilios\CoreBundle\Tests\Model;

use Ilios\CoreBundle\Model\AamcMethod;

/**
 * Tests for Model AamcMethod
 */
class AamcMethodTest extends ModelBase
{
    /**
     * @var AamcMethod
     */
    protected $object;

    /**
     * Instantiate a AamcMethod object
     */
    protected function setUp()
    {
        $this->object = new AamcMethod;
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::__construct
     */
    public function testConstructor()
    {
        $this->assertTrue($this->object->getSessionTypes()->isEmpty());
    }
    
    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::setMethodId
     */
    public function testSetMethodId()
    {
        $this->basicSetTest('id', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::getMethodId
     */
    public function testGetMethodId()
    {
        $this->basicGetTest('id', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::addSessionType
     */
    public function testAddSessionType()
    {
        $this->modelCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->modelCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers Ilios\CoreBundle\Model\AamcMethod::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->modelCollectionGetTest('sessionType', 'SessionType');
    }
}
