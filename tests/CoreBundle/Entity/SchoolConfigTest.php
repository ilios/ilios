<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\SchoolConfig;
use Mockery as m;

/**
 * Tests for SchoolConfig entity.
 */
class SchoolConfigTest extends EntityBase
{
    /**
     * @var SchoolConfig
     */
    protected $object;

    /**
     * Instantiate a SchoolConfig object
     */
    protected function setUp()
    {
        $this->object = new SchoolConfig();
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::setName
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::setValue
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::setValue
     * @covers \Ilios\CoreBundle\Entity\SchoolConfig::getValue
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
