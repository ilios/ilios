<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\InstructionHours;
use Mockery as m;

/**
 * Tests for Model InstructionHours
 */
class InstructionHoursTest extends BaseModel
{
    /**
     * @var InstructionHours
     */
    protected $object;

    /**
     * Instantiate a InstructionHours object
     */
    protected function setUp()
    {
        $this->object = new InstructionHours;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getInstructionHoursId
     */
    public function testGetInstructionHoursId()
    {
        $this->basicGetTest('instructionHoursId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setGenerationTimeStamp
     */
    public function testSetGenerationTimeStamp()
    {
        $this->basicSetTest('generationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getGenerationTimeStamp
     */
    public function testGetGenerationTimeStamp()
    {
        $this->basicGetTest('generationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setHoursAccrued
     */
    public function testSetHoursAccrued()
    {
        $this->basicSetTest('hoursAccrued', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getHoursAccrued
     */
    public function testGetHoursAccrued()
    {
        $this->basicGetTest('hoursAccrued', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setModified
     */
    public function testSetModified()
    {
        $this->basicSetTest('modified', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getModified
     */
    public function testGetModified()
    {
        $this->basicGetTest('modified', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setModificationTimeStamp
     */
    public function testSetModificationTimeStamp()
    {
        $this->basicSetTest('modificationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getModificationTimeStamp
     */
    public function testGetModificationTimeStamp()
    {
        $this->basicGetTest('modificationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\InstructionHours::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'int');
    }
}
