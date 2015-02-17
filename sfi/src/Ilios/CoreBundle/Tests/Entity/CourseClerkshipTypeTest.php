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
     * @covers Ilios\CoreBundle\Entity\CourseClerkshipType::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }
}
