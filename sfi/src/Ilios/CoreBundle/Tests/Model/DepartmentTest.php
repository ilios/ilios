<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Department;
use Mockery as m;

/**
 * Tests for Model Department
 */
class DepartmentTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\Department::getDepartmentId
     */
    public function testGetDepartmentId()
    {
        $this->basicGetTest('departmentId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::setTitle
     */
    public function testSetTitle()
    {
        $this->basicSetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::getTitle
     */
    public function testGetTitle()
    {
        $this->basicGetTest('title', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::setSchool
     */
    public function testSetSchool()
    {
        $this->modelSetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::getSchool
     */
    public function testGetSchool()
    {
        $this->modelGetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::setDeleted
     */
    public function testSetDeleted()
    {
        $this->basicSetTest('deleted', 'boolean');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Department::getDeleted
     */
    public function testGetDeleted()
    {
        $this->basicGetTest('deleted', 'boolean');
    }
}
