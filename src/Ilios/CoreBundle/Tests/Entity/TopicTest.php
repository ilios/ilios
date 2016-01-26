<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Topic;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * @deprecated
 * Tests for Entity Topic
 */
class TopicTest extends EntityBase
{
    /**
     * @var Topic
     */
    protected $object;

    /**
     * Instantiate a Topic object
     */
    protected function setUp()
    {
        $this->object = new Topic;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
        $this->assertEmpty($this->object->getProgramYears());
        $this->assertEmpty($this->object->getSessions());
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::setTitle
     * @covers Ilios\CoreBundle\Entity\Topic::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::setSchool
     * @covers Ilios\CoreBundle\Entity\Topic::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->entityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->entityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addSession
     */
    public function testAddSession()
    {
        $this->entityCollectionAddTest('session', 'Session', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getSessions
     */
    public function testGetSessions()
    {
        $this->entityCollectionSetTest('session', 'Session', false, false, 'addTopic');
    }
}
