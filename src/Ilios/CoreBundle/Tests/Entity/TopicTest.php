<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Topic;
use Mockery as m;
use Doctrine\Common\Collections\ArrayCollection;

/**
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
        $this->softDeleteEntitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addCourse
     */
    public function testAddCourse()
    {
        $this->softDeleteEntityCollectionAddTest('course', 'Course', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getCourses
     */
    public function testGetCourses()
    {
        $this->softDeleteEntityCollectionSetTest('course', 'Course', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addProgramYear
     */
    public function testAddProgramYear()
    {
        $this->softDeleteEntityCollectionAddTest('programYear', 'ProgramYear', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getProgramYears
     */
    public function testGetProgramYears()
    {
        $this->softDeleteEntityCollectionSetTest('programYear', 'ProgramYear', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::addSession
     */
    public function testAddSession()
    {
        $this->softDeleteEntityCollectionAddTest('session', 'Session', false, false, 'addTopic');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Topic::getSessions
     */
    public function testGetSessions()
    {
        $this->softDeleteEntityCollectionSetTest('session', 'Session', false, false, 'addTopic');
    }
}
