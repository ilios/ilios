<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\CiSessions;
use Mockery as m;

/**
 * Tests for Entity CiSessions
 */
class CiSessionsTest extends EntityBase
{
    /**
     * @var CiSessions
     */
    protected $object;

    /**
     * Instantiate a CiSessions object
     */
    protected function setUp()
    {
        $this->object = new CiSessions;
    }
    

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::setIpAddress
     */
    public function testSetIpAddress()
    {
        $this->basicSetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::getIpAddress
     */
    public function testGetIpAddress()
    {
        $this->basicGetTest('ipAddress', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::setUserAgent
     */
    public function testSetUserAgent()
    {
        $this->basicSetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::getUserAgent
     */
    public function testGetUserAgent()
    {
        $this->basicGetTest('userAgent', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::setLastActivity
     */
    public function testSetLastActivity()
    {
        $this->basicSetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::getLastActivity
     */
    public function testGetLastActivity()
    {
        $this->basicGetTest('lastActivity', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::setUserData
     */
    public function testSetUserData()
    {
        $this->basicSetTest('userData', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\CiSessions::getUserData
     */
    public function testGetUserData()
    {
        $this->basicGetTest('userData', 'string');
    }
}
