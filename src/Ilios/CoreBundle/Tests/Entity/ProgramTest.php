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
     * @covers Ilios\CoreBundle\Entity\Program::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setShortTitle
     * @covers Ilios\CoreBundle\Entity\Program::getShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDuration
     * @covers Ilios\CoreBundle\Entity\Program::getDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDeleted
     * @covers Ilios\CoreBundle\Entity\Program::isDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishedAsTbd
     * @covers Ilios\CoreBundle\Entity\Program::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setOwningSchool
     * @covers Ilios\CoreBundle\Entity\Program::getOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishEvent
     * @covers Ilios\CoreBundle\Entity\Program::getPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
