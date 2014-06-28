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
     * @covers Ilios\CoreBundle\Entity\Program::getProgramId
     */
    public function testGetProgramId()
    {
        $this->basicGetTest('programId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getShortTitle
     */
    public function testGetShortTitle()
    {
        $this->basicGetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getDuration
     */
    public function testGetDuration()
    {
        $this->basicGetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->entityGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->entityGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
