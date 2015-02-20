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
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAamcCode
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAamcCode
     */
    public function testSetAamcCode()
    {
        $this->basicSetTest('aamcCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStreet
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressStreet
     */
    public function testSetAddressStreet()
    {
        $this->basicSetTest('addressStreet', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCity
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressCity
     */
    public function testSetAddressCity()
    {
        $this->basicSetTest('addressCity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressStateOrProvince
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressStateOrProvince
     */
    public function testSetAddressStateOrProvince()
    {
        $this->basicSetTest('addressStateOrProvince', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressZipcode
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressZipcode
     */
    public function testSetAddressZipcode()
    {
        $this->basicSetTest('addressZipcode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setAddressCountryCode
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getAddressCountryCode
     */
    public function testSetAddressCountryCode()
    {
        $this->basicSetTest('addressCountryCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::setSchool
     * @covers Ilios\CoreBundle\Entity\CurriculumInventoryInstitution::getSchool
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
