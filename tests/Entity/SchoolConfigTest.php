<?php
namespace Tests\App\Entity;

use App\Entity\SchoolConfig;
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
     * @covers \App\Entity\SchoolConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \App\Entity\SchoolConfig::setName
     * @covers \App\Entity\SchoolConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\SchoolConfig::setValue
     * @covers \App\Entity\SchoolConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }

    /**
     * @covers \App\Entity\SchoolConfig::setValue
     * @covers \App\Entity\SchoolConfig::getValue
     */
    public function testSetSchool()
    {
        $this->entitySetTest('school', 'School');
    }
}
