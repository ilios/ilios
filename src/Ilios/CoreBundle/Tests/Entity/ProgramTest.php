<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Program;
use Mockery as m;

/**
 * Tests for Entity Program
 */
class ProgramTest extends EntityBase
{
    /**
     * @var Program
     */
    protected $object;

    /**
     * Instantiate a Program object
     */
    protected function setUp()
    {
        $this->object = new Program;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::isDeleted
     */
    public function testIsDeleted()
    {
        $this->booleanSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
