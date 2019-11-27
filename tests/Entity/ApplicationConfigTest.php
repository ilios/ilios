<?php
namespace App\Tests\Entity;

use App\Entity\ApplicationConfig;
use Mockery as m;

/**
 * Tests for ApplicationConfig entity.
 * @group model
 */
class ApplicationConfigTest extends EntityBase
{
    /**
     * @var ApplicationConfig
     */
    protected $object;

    /**
     * Instantiate a ApplicationConfig object
     */
    protected function setUp()
    {
        $this->object = new ApplicationConfig();
    }

    /**
     * @covers \App\Entity\ApplicationConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \App\Entity\ApplicationConfig::setName
     * @covers \App\Entity\ApplicationConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \App\Entity\ApplicationConfig::setValue
     * @covers \App\Entity\ApplicationConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
}
