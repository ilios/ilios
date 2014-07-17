<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\InstructionHours;
use Mockery as m;

/**
 * Tests for Entity InstructionHours
 */
class InstructionHoursTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getInstructionHoursId
     */
    public function testGetInstructionHoursId()
    {
        $this->basicGetTest('instructionHoursId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setGenerationTimeStamp
     */
    public function testSetGenerationTimeStamp()
    {
        $this->basicSetTest('generationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getGenerationTimeStamp
     */
    public function testGetGenerationTimeStamp()
    {
        $this->basicGetTest('generationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setHoursAccrued
     */
    public function testSetHoursAccrued()
    {
        $this->basicSetTest('hoursAccrued', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getHoursAccrued
     */
    public function testGetHoursAccrued()
    {
        $this->basicGetTest('hoursAccrued', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setModified
     */
    public function testSetModified()
    {
        $this->basicSetTest('modified', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getModified
     */
    public function testGetModified()
    {
        $this->basicGetTest('modified', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setModificationTimeStamp
     */
    public function testSetModificationTimeStamp()
    {
        $this->basicSetTest('modificationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getModificationTimeStamp
     */
    public function testGetModificationTimeStamp()
    {
        $this->basicGetTest('modificationTimeStamp', 'datetime');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setUserId
     */
    public function testSetUserId()
    {
        $this->basicSetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getUserId
     */
    public function testGetUserId()
    {
        $this->basicGetTest('userId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\InstructionHours::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }
}
