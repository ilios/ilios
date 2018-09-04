<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CourseClerkshipType;
use Mockery as m;

/**
 * Tests for Entity CourseClerkshipType
 */
class CourseClerkshipTypeTest extends EntityBase
{
    /**
     * @var CourseClerkshipType
     */
    protected $object;

    /**
     * Instantiate a CourseClerkshipType object
     */
    protected function setUp()
    {
        $this->object = new CourseClerkshipType;
    }

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'title'
        );
        $this->validateNotBlanks($notBlank);

        $this->object->setTitle('20 max title');
        $this->validate(0);
    }

    /**
     * @covers \AppBundle\Entity\CourseClerkshipType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
    }

    /**
     * @covers \AppBundle\Entity\CourseClerkshipType::setTitle
     * @covers \AppBundle\Entity\CourseClerkshipType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'setClerkshipType');
    }

    /**
     * @covers \AppBundle\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \AppBundle\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'setClerkshipType');
    }
}
