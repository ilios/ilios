<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\ApplicationConfig;
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
     * @covers \Ilios\CoreBundle\Entity\ApplicationConfig::__construct
     */
    public function testConstructor()
    {
        $this->assertEmpty($this->object->getValue());
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ApplicationConfig::setName
     * @covers \Ilios\CoreBundle\Entity\ApplicationConfig::getName
     */
    public function testSetName()
    {
        $this->basicSetTest('name', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\ApplicationConfig::setValue
     * @covers \Ilios\CoreBundle\Entity\ApplicationConfig::getValue
     */
    public function testSetValue()
    {
        $this->basicSetTest('value', 'string');
    }
}
