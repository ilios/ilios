<?php
namespace Ilios\CoreBundle\Tests\Entity;

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

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
