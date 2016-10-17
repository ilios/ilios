<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\CurriculumInventoryInstitution;
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
        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));
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

        $this->object->setSchool(m::mock('Ilios\CoreBundle\Entity\SchoolInterface'));
        $this->validate(0);
    }


    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setName
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAamcCode
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStreet
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCity
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressZipcode
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setSchool
     * @covers \Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
