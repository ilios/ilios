<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Ilios\CoreBundle\Entity\Department;
use Mockery as m;

/**
 * Tests for Entity Department
 */
class DepartmentTest extends EntityBase
{
    /**
     * @var Department
     */
    protected $object;

    /**
     * Instantiate a Department object
     */
    protected function setUp()
    {
        $this->object = new Department;
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::setSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
