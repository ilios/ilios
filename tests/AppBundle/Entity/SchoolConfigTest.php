<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\SchoolConfig;
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
     * @covers \AppBundle\Entity\SchoolConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \AppBundle\Entity\SchoolConfig::setName
     * @covers \AppBundle\Entity\SchoolConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\SchoolConfig::setValue
     * @covers \AppBundle\Entity\SchoolConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \AppBundle\Entity\SchoolConfig::setValue
     * @covers \AppBundle\Entity\SchoolConfig::getValue
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
