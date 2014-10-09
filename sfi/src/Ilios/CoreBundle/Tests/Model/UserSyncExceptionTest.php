<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\UserSyncException;
use Mockery as m;

/**
 * Tests for Model UserSyncException
 */
class UserSyncExceptionTest extends ModelBase
{
    /**
     * @var UserSyncException
     */
    protected $object;

    /**
     * Instantiate a UserSyncException object
     */
    protected function setUp()
    {
        $this->object = new UserSyncException;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getExceptionId
     */
    public function testGetExceptionId()
    {
        $this->basicGetTest('exceptionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setProcessId
     */
    public function testSetProcessId()
    {
        $this->basicSetTest('processId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getProcessId
     */
    public function testGetProcessId()
    {
        $this->basicGetTest('processId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setProcessName
     */
    public function testSetProcessName()
    {
        $this->basicSetTest('processName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getProcessName
     */
    public function testGetProcessName()
    {
        $this->basicGetTest('processName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setExceptionCode
     */
    public function testSetExceptionCode()
    {
        $this->basicSetTest('exceptionCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getExceptionCode
     */
    public function testGetExceptionCode()
    {
        $this->basicGetTest('exceptionCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setMismatchedPropertyName
     */
    public function testSetMismatchedPropertyName()
    {
        $this->basicSetTest('mismatchedPropertyName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getMismatchedPropertyName
     */
    public function testGetMismatchedPropertyName()
    {
        $this->basicGetTest('mismatchedPropertyName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setMismatchedPropertyValue
     */
    public function testSetMismatchedPropertyValue()
    {
        $this->basicSetTest('mismatchedPropertyValue', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getMismatchedPropertyValue
     */
    public function testGetMismatchedPropertyValue()
    {
        $this->basicGetTest('mismatchedPropertyValue', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\UserSyncException::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', 'User');
    }
}
