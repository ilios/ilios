<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CourseClerkshipType;
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
    

    /**
     * @covers Ilios\CoreBundle\Entity\CourseClerkshipType::getCourseClerkshipTypeId
     */
    public function testGetCourseClerkshipTypeId()
    {
        $this->basicGetTest('courseClerkshipTypeId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseClerkshipType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CourseClerkshipType::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }
}
