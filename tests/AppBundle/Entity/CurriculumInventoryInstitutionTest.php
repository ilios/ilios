<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\CurriculumInventoryInstitution;
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
        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));
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

        $this->object->setSchool(m::mock('AppBundle\Entity\SchoolInterface'));
        $this->validate(0);
    }


    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setName
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAamcCode
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAddressStreet
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAddressCity
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAddressZipcode
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::setSchool
     * @covers \AppBundle\Entity\CurriculumInventoryInstitution::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
