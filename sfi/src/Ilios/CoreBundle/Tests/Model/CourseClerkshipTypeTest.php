<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CourseClerkshipType;
use Mockery as m;

/**
 * Tests for Model CourseClerkshipType
 */
class CourseClerkshipTypeTest extends BaseModel
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
    

    /**
     * @covers Ilios\CoreBundle\Model\CourseClerkshipType::getCourseClerkshipTypeId
     */
    public function testGetCourseClerkshipTypeId()
    {
        $this->basicGetTest('courseClerkshipTypeId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseClerkshipType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CourseClerkshipType::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }
}
