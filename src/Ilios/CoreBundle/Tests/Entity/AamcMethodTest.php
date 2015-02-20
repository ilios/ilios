<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\AamcMethod;
use Mockery as m;

/**
 * Tests for Entity AamcMethod
 */
class AamcMethodTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\AamcMethod::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getSessionTypes());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcMethod::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcMethod::addSessionType
     */
    public function testAddSessionType()
    {
        $this->entityCollectionAddTest('sessionType', 'SessionType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcMethod::removeSessionType
     */
    public function testRemoveSessionType()
    {
        $this->entityCollectionRemoveTest('sessionType', 'SessionType');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\AamcMethod::getSessionTypes
     */
    public function testGetSessionTypes()
    {
        $this->entityCollectionSetTest('sessionType', 'SessionType');
    }
}
