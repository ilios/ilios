<?php
namespace Ilios\CoreBundle\Tests\Entity;

use Mockery as m;

use Ilios\CoreBundle\Entity\CISession;

/**
 * Tests for Entity CISession
 */
class CISessionTest extends EntityBase
{
    /**
     * @var CISession
     */
    protected $object;
    
    protected $legacyUtilities;

    /**
     * Instantiate a CISession object
     */
    protected function setUp()
    {
        $this->object = new CISession;
        $this->legacyUtilities = m::mock('Ilios\LegacyCIBundle\Utilities');
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $container->shouldReceive('get')->with('ilios_legacy.utilities')->andReturn($this->legacyUtilities);
        $this->object->setContainer($container);
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setIpAddress
     * @covers Ilios\CoreBundle\Entity\CISession::getIpAddress
     */
    public function testSetIpAddress()
    {
        $this->basicSetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setUserAgent
     * @covers Ilios\CoreBundle\Entity\CISession::getUserAgent
     */
    public function testSetUserAgent()
    {
        $this->basicSetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setLastActivity
     * @covers Ilios\CoreBundle\Entity\CISession::getLastActivity
     */
    public function testSetLastActivity()
    {
        $this->basicSetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setUserData
     * @covers Ilios\CoreBundle\Entity\CISession::getUserData
     * @covers Ilios\CoreBundle\Entity\CISession::getUserData
     */
    public function testSetUserData()
    {
        $faker = \Faker\Factory::create();
        $data = array();
        for ($i=0; $i<25; $i++) {
            $data[$faker->text] = $faker->randomElements();
        }
        $this->legacyUtilities->shouldReceive('serialize')->with($data)->times(1);
        $this->object->setUserData($data);
        $this->legacyUtilities->shouldReceive('unserialize')->times(1)->andReturn($data);
        $this->assertSame($data, $this->object->getUserData());
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getUserDataItem
     * @covers Ilios\CoreBundle\Entity\CISession::getUnserializedUserData
     */
    public function testGetUserDataItem()
    {
        $data = array('foo' => 'bar');
        $this->legacyUtilities->shouldReceive('serialize')->with($data)->times(1);
        $this->object->setUserData($data);
        $this->legacyUtilities->shouldReceive('unserialize')->times(1)->andReturn($data);
        $this->assertSame('bar', $this->object->getUserDataItem('foo'));
    }
    
    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getUserDataItem
     * @covers Ilios\CoreBundle\Entity\CISession::getUnserializedUserData
     */
    public function testGetUserDataItemNoData()
    {
        $this->legacyUtilities->shouldReceive('unserialize')->times(1)->andReturn(array());
        $this->assertFalse($this->object->getUserDataItem('foo'));
    }
}
