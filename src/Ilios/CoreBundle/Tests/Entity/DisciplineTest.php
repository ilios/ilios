<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Discipline;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Tests for Entity Discipline
 */
class DisciplineTest extends EntityBase
{
    /**
     * @var Discipline
     */
    protected $object;

    /**
     * Instantiate a Discipline object
     */
    protected function setUp()
    {
        $this->object = new Discipline;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::setTitle
     * @covers Ilios\CoreBundle\Entity\Discipline::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::setOwningSchool
     * @covers Ilios\CoreBundle\Entity\Discipline::getOwningSchool
     */
    public function testSetOwningSchool()
    {
        $this->entitySetTest('owningSchool', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addCourse
     */
    public function testAddCourse()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $this->object->addCourse($goodCourse);
        $this->object->addCourse($deletedCourse);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getCourses
     */
    public function testGetCourses()
    {
        $goodCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedCourse = m::mock('Ilios\CoreBundle\Entity\Course')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $collection = new ArrayCollection([$goodCourse, $deletedCourse]);
        $this->object->setCourses($collection);
        $results = $this->object->getCourses();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodCourse));
        $this->assertFalse($results->contains($deletedCourse));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::addSession
     */
    public function testAddSession()
    {
        $goodSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $this->object->addSession($goodSession);
        $this->object->addSession($deletedSession);
        $results = $this->object->getSessions();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodSession));
        $this->assertFalse($results->contains($deletedSession));
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Discipline::getSessions
     */
    public function testGetSessions()
    {
        $goodSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(false)
            ->mock();
        $deletedSession = m::mock('Ilios\CoreBundle\Entity\Session')
            ->shouldReceive('isDeleted')->withNoArgs()->andReturn(true)
            ->mock();
        $collection = new ArrayCollection([$goodSession, $deletedSession]);
        $this->object->setSessions($collection);
        $results = $this->object->getSessions();
        $this->assertTrue($results instanceof ArrayCollection, 'Collection not returned.');

        $this->assertTrue($results->contains($goodSession));
        $this->assertFalse($results->contains($deletedSession));
    }
}
