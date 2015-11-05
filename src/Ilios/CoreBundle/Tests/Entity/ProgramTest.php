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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'shortTitle',
            'duration'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setShortTitle('DVc');
        $this->object->setDuration(30);
        $this->validate(0);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getCurriculumInventoryReports());
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
     * @covers Ilios\CoreBundle\Entity\Program::setPublishedAsTbd
     * @covers Ilios\CoreBundle\Entity\Program::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Program::setSchool
     * @covers Ilios\CoreBundle\Entity\Program::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
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
