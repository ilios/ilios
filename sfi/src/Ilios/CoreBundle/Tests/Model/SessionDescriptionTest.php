<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\SessionDescription;
use Mockery as m;

/**
 * Tests for Model SessionDescription
 */
class SessionDescriptionTest extends BaseModel
{
    /**
     * @var SessionDescription
     */
    protected $object;

    /**
     * Instantiate a SessionDescription object
     */
    protected function setUp()
    {
        $this->object = new SessionDescription;
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::setSessionId
     */
    public function testSetSessionId()
    {
        $this->basicSetTest('sessionId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::getSessionId
     */
    public function testGetSessionId()
    {
        $this->basicGetTest('sessionId', 'int');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::setDescription
     */
    public function testSetDescription()
    {
        $this->basicSetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::getDescription
     */
    public function testGetDescription()
    {
        $this->basicGetTest('description', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::setSession
     */
    public function testSetSession()
    {
        $this->modelSetTest('session', 'Session');
    }

    /**
     * @covers Ilios\CoreBundle\Model\SessionDescription::getSession
     */
    public function testGetSession()
    {
        $this->modelGetTest('session', 'Session');
    }
}
