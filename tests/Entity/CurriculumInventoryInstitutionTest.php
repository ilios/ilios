<?php

declare(strict_types=1);

namespace App\Tests\Entity;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\CoversClass;
use App\Entity\CurriculumInventoryInstitution;
use App\Entity\SchoolInterface;
use Mockery as m;

/**
 * Tests for Entity CurriculumInventoryInstitution
 */
#[Group('model')]
#[CoversClass(CurriculumInventoryInstitution::class)]
final class CurriculumInventoryInstitutionTest extends EntityBase
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


    public function testSetName(): void
    {
        $this->basicSetTest('name', 'string');
    }

    public function testSetAamcCode(): void
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    public function testSetAddressStreet(): void
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    public function testSetAddressCity(): void
    {
        $this->basicSetTest('addressCity', 'string');
    }

    public function testSetAddressStateOrProvince(): void
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    public function testSetAddressZipcode(): void
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    public function testSetAddressCountryCode(): void
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    public function testSetSchool(): void
    {
        $this->entitySetTest('school', 'School');
    }

    protected function getObject(): CurriculumInventoryInstitution
    {
        return $this->object;
    }
}
