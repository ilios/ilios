<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\CurriculumInventoryInstitution;
use Mockery as m;

/**
 * Tests for Model CurriculumInventoryInstitution
 */
class CurriculumInventoryInstitutionTest extends ModelBase
{
    /**
     * @var CurriculumInventoryInstitution
     */
    protected $object;

    /**
     * Instantiate a CurriculumInventoryInstitution object
     */
    protected function setUp()
    {
        $this->object = new CurriculumInventoryInstitution;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setSchoolId
     */
    public function testSetSchoolId()
    {
        $this->basicSetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getSchoolId
     */
    public function testGetSchoolId()
    {
        $this->basicGetTest('schoolId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getName
     */
    public function testGetName()
    {
        $this->basicGetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAamcCode
     */
    public function testGetAamcCode()
    {
        $this->basicGetTest('aamcCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testGetAddressStreet()
    {
        $this->basicGetTest('addressStreet', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAddressCity
     */
    public function testGetAddressCity()
    {
        $this->basicGetTest('addressCity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testGetAddressStateOrProvince()
    {
        $this->basicGetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testGetAddressZipcode()
    {
        $this->basicGetTest('addressZipcode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testGetAddressCountryCode()
    {
        $this->basicGetTest('addressCountryCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::setSchool
     */
    public function testSetSchool()
    {
        $this->modelSetTest('school', 'School');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CurriculumInventoryInstitution::getSchool
     */
    public function testGetSchool()
    {
        $this->modelGetTest('school', 'School');
    }
}
