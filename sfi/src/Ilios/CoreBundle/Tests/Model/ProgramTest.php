<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Program;
use Mockery as m;

/**
 * Tests for Model Program
 */
class ProgramTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\Program::getProgramId
     */
    public function testGetProgramId()
    {
        $this->basicGetTest('programId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setShortTitle
     */
    public function testSetShortTitle()
    {
        $this->basicSetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getShortTitle
     */
    public function testGetShortTitle()
    {
        $this->basicGetTest('shortTitle', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setDuration
     */
    public function testSetDuration()
    {
        $this->basicSetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getDuration
     */
    public function testGetDuration()
    {
        $this->basicGetTest('duration', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted', 'boolean');
    }

    /**
<<<<<<< HEAD:sfi/src/Ilios/CoreBundle/Tests/Entity/ProgramTest.php
     * @covers Ilios\CoreBundle\Entity\Program::isDeleted
=======
     * @covers Ilios\CoreBundle\Model\Program::getDeleted
>>>>>>> Changed entity to model. Cleaning up more models.:sfi/src/Ilios/CoreBundle/Tests/Model/ProgramTest.php
     */
    public function testIsDeleted()
    {
        $this->basicIsTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->basicSetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getPublishedAsTbd
     */
    public function testGetPublishedAsTbd()
    {
        $this->basicGetTest('publishedAsTbd', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->modelSetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getOwningSchool
     */
    public function testGetOwningSchool()
    {
         $this->modelGetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::getPublishEvent
     */
    public function testGetPublishEvent()
    {
         $this->modelGetTest('publishEvent', 'PublishEvent');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Program::setPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->modelSetTest('publishEvent', 'PublishEvent');
    }
}
