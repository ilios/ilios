<?php
namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryInstitution;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryInstitution
 */
class CurriculumInventoryInstitutionTest extends EntityBase
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

    public function testNotBlankValidation()
    {
        $notBlank = array(
            'name',
            'aamcCode',
            'addressStreet',
            'addressCity',
            'addressStateOrProvince',
            'addressZipCode',
            'addressCountryCode'
        );
        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));
        $this->validateNotBlanks($notBlank);

        $this->object->setName('10lenMAX');
        $this->object->setAamcCode('ddd');
        $this->object->setAddressStreet('1123 A');
        $this->object->setAddressCity('Irvine');
        $this->object->setAddressStateOrProvince('CA');
        $this->object->setAddressZipcode('99999');
        $this->object->setAddressCountryCode('US');
        $this->validate(0);
    }

    public function testNotNullValidation()
    {
        $notNull = array(
            'school'
        );
        $this->object->setName('10lenMAX');
        $this->object->setAamcCode('ddd');
        $this->object->setAddressStreet('1123 A');
        $this->object->setAddressCity('Irvine');
        $this->object->setAddressStateOrProvince('CA');
        $this->object->setAddressZipcode('99999');
        $this->object->setAddressCountryCode('US');
        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock('App\Entity\SchoolInterface'));
        $this->validate(0);
    }


    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setName
     * @covers \App\Entity\CurriculumInventoryInstitution::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAamcCode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressStreet
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressCity
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressZipcode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setSchool
     * @covers \App\Entity\CurriculumInventoryInstitution::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
