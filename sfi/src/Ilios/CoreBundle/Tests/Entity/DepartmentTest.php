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
     * @covers Ilios\CoreBundle\Entity\Department::getDepartmentId
     */
    public function testGetDepartmentId()
    {
        $this->basicGetTest('departmentId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::setSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::getSchool
     */
    public function testGetSchool()
    {
        $this->entityGetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\Department::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }
}
