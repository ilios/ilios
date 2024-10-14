<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use App\Entity\CurriculumInventoryInstitution;
use App\Entity\SchoolInterface;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryInstitution
 * @group model
 */
class CurriculumInventoryInstitutionTest extends EntityBase
{
    protected CurriculumInventoryInstitution $object;

    protected function setUp(): void
    {
        parent::setUp();
        $this->object = new CurriculumInventoryInstitution();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->object);
    }

    public function testNotBlankValidation(): void
    {
        $notBlank = [
            'name',
            'aamcCode',
            'addressStreet',
            'addressCity',
            'addressStateOrProvince',
            'addressZipCode',
            'addressCountryCode',
        ];
        $this->object->setSchool(m::mock(SchoolInterface::class));
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

    public function testNotNullValidation(): void
    {
        $notNull = [
            'school',
        ];
        $this->object->setName('10lenMAX');
        $this->object->setAamcCode('ddd');
        $this->object->setAddressStreet('1123 A');
        $this->object->setAddressCity('Irvine');
        $this->object->setAddressStateOrProvince('CA');
        $this->object->setAddressZipcode('99999');
        $this->object->setAddressCountryCode('US');
        $this->validateNotNulls($notNull);

        $this->object->setSchool(m::mock(SchoolInterface::class));
        $this->validate(0);
    }


    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setName
     * @covers \App\Entity\CurriculumInventoryInstitution::getName
     */
    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAamcCode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAamcCode
     */
    public function testSetAamcCode(): void
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressStreet
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testSetAddressStreet(): void
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressCity
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressCity
     */
    public function testSetAddressCity(): void
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince(): void
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressZipcode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testSetAddressZipcode(): void
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     * @covers \App\Entity\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testSetAddressCountryCode(): void
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers \App\Entity\CurriculumInventoryInstitution::setSchool
     * @covers \App\Entity\CurriculumInventoryInstitution::getSchool
     */
    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    protected function getObject(): CurriculumInventoryInstitution
    {
        return $this->object;
    }
}
