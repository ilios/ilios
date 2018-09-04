<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\ApplicationConfig;
use Mockery as m;

/**
 * Tests for ApplicationConfig entity.
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
     * @covers \AppBundle\Entity\ApplicationConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \AppBundle\Entity\ApplicationConfig::setName
     * @covers \AppBundle\Entity\ApplicationConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \AppBundle\Entity\ApplicationConfig::setValue
     * @covers \AppBundle\Entity\ApplicationConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
}
