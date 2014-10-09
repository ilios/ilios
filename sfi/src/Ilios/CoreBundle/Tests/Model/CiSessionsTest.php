<?php
namespace Ilios\CoreBundle\Tests\Model;

use Mockery as m;

use Ilios\CoreBundle\Model\CISession;

/**
 * Tests for Model CISession
 */
class CISessionTest extends ModelBase
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
     * @covers Ilios\CoreBundle\Model\CISession::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::setIpAddress
     */
    public function testSetIpAddress()
    {
        $this->basicSetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::getIpAddress
     */
    public function testGetIpAddress()
    {
        $this->basicGetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::setUserAgent
     */
    public function testSetUserAgent()
    {
        $this->basicSetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::getUserAgent
     */
    public function testGetUserAgent()
    {
        $this->basicGetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::setLastActivity
     */
    public function testSetLastActivity()
    {
        $this->basicSetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::getLastActivity
     */
    public function testGetLastActivity()
    {
        $this->basicGetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\CISession::setUserData
     * @covers Ilios\CoreBundle\Model\CISession::getUserData
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
     * @covers Ilios\CoreBundle\Model\CISession::getUserDataItem
     * @covers Ilios\CoreBundle\Model\CISession::getUnserializedUserData
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
     * @covers Ilios\CoreBundle\Model\CISession::getUserDataItem
     * @covers Ilios\CoreBundle\Model\CISession::getUnserializedUserData
     */
    public function testGetUserDataItemNoData()
    {
        $this->legacyUtilities->shouldReceive('unserialize')->times(1)->andReturn(array());
        $this->assertFalse($this->object->getUserDataItem('foo'));
    }
}
