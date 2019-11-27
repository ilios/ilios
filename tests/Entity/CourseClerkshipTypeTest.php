<?php
namespace App\Tests\Entity;

use App\Entity\CourseClerkshipType;
use Mockery as m;

/**
 * Tests for Entity CourseClerkshipType
 * @group model
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
     * @covers \App\Entity\CourseClerkshipType::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getCourses());
    }

    /**
     * @covers \App\Entity\CourseClerkshipType::setTitle
     * @covers \App\Entity\CourseClerkshipType::getTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers \App\Entity\Objective::addCourse
     */
    public function testAddCourse()
    {
        $this->entityCollectionAddTest('course', 'Course', false, false, 'setClerkshipType');
    }

    /**
     * @covers \App\Entity\Objective::removeCourse
     */
    public function testRemoveCourse()
    {
        $this->entityCollectionRemoveTest('course', 'Course');
    }

    /**
     * @covers \App\Entity\Objective::getCourses
     */
    public function testGetCourses()
    {
        $this->entityCollectionSetTest('course', 'Course', false, false, 'setClerkshipType');
    }
}
