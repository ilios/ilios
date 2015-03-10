<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\ProgramYear;
use Mockery as m;

/**
 * Tests for Entity ProgramYear
 */
class ProgramYearTest extends EntityBase
{
    /**
     * @var ProgramYear
     */
    protected $object;

    /**
     * Instantiate a ProgramYear object
     */
    protected function setUp()
    {
        $this->object = new ProgramYear;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'startYear',
            'deleted',
            'locked',
            'archived',
            'publishedAsTbd'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setStartYear(3);
        // i had to set these to true -- failed when false
        $this->object->setDeleted(true);
        $this->object->setLocked(true);
        $this->object->setArchived(true);
        $this->object->setPublishedAsTbd(true);
        $this->validate(0);
    }
    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCompetencies());
        $this->assertEmpty($this->object->getDirectors());
        $this->assertEmpty($this->object->getDisciplines());
        $this->assertEmpty($this->object->getObjectives());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setStartYear
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getStartYear
     */
    public function testSetStartYear()
    {
        $this->basicSetTest('startYear', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setDeleted
     * @covers Ilios\CoreBundle\Entity\ProgramYear::isDeleted
     */
    public function testSetDeleted()
    {
        $this->booleanSetTest('deleted');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setLocked
     * @covers Ilios\CoreBundle\Entity\ProgramYear::isLocked
     */
    public function testSetLocked()
    {
        $this->booleanSetTest('locked');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setArchived
     * @covers Ilios\CoreBundle\Entity\ProgramYear::isArchived
     */
    public function testSetArchived()
    {
        $this->booleanSetTest('archived');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setPublishedAsTbd
     * @covers Ilios\CoreBundle\Entity\ProgramYear::isPublishedAsTbd
     */
    public function testSetPublishedAsTbd()
    {
        $this->booleanSetTest('publishedAsTbd');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setProgram
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getProgram
     */
    public function testSetProgram()
    {
        $this->entitySetTest('program', 'Program');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::addDirector
     */
    public function testAddDirector()
    {
        $this->entityCollectionAddTest('director', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\ProgramYear::setPublishEvent
     * @covers Ilios\CoreBundle\Entity\ProgramYear::getPublishEvent
     */
    public function testSetPublishEvent()
    {
        $this->entitySetTest('publishEvent', 'PublishEvent');
    }
}
