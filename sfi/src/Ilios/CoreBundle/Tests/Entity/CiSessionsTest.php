<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CISession;
use Mockery as m;

/**
 * Tests for Entity CISession
 */
class CISessionTest extends EntityBase
{
    /**
     * @var CISession
     */
    protected $object;

    /**
     * Instantiate a CISession object
     */
    protected function setUp()
    {
        $this->object = new CISession;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setIpAddress
     */
    public function testSetIpAddress()
    {
        $this->basicSetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getIpAddress
     */
    public function testGetIpAddress()
    {
        $this->basicGetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setUserAgent
     */
    public function testSetUserAgent()
    {
        $this->basicSetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getUserAgent
     */
    public function testGetUserAgent()
    {
        $this->basicGetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setLastActivity
     */
    public function testSetLastActivity()
    {
        $this->basicSetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getLastActivity
     */
    public function testGetLastActivity()
    {
        $this->basicGetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::setUserData
     */
    public function testSetUserData()
    {
        $this->basicSetTest('userData', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CISession::getUserData
     */
    public function testGetUserData()
    {
        $this->basicGetTest('userData', 'string');
    }
}
