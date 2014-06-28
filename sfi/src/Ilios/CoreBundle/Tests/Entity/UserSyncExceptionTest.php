<?php
namespace Ilios\CoreBundle\Tests\Entity;


use Ilios\CoreBundle\Entity\UserSyncException;
use Mockery as m;

/**
 * Tests for Entity UserSyncException
 */
class UserSyncExceptionTest extends EntityBase
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
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getExceptionId
     */
    public function testGetExceptionId()
    {
        $this->basicGetTest('exceptionId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setProcessId
     */
    public function testSetProcessId()
    {
        $this->basicSetTest('processId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getProcessId
     */
    public function testGetProcessId()
    {
        $this->basicGetTest('processId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setProcessName
     */
    public function testSetProcessName()
    {
        $this->basicSetTest('processName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getProcessName
     */
    public function testGetProcessName()
    {
        $this->basicGetTest('processName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setExceptionCode
     */
    public function testSetExceptionCode()
    {
        $this->basicSetTest('exceptionCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getExceptionCode
     */
    public function testGetExceptionCode()
    {
        $this->basicGetTest('exceptionCode', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setMismatchedPropertyName
     */
    public function testSetMismatchedPropertyName()
    {
        $this->basicSetTest('mismatchedPropertyName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getMismatchedPropertyName
     */
    public function testGetMismatchedPropertyName()
    {
        $this->basicGetTest('mismatchedPropertyName', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setMismatchedPropertyValue
     */
    public function testSetMismatchedPropertyValue()
    {
        $this->basicSetTest('mismatchedPropertyValue', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getMismatchedPropertyValue
     */
    public function testGetMismatchedPropertyValue()
    {
        $this->basicGetTest('mismatchedPropertyValue', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::setUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Entity\UserSyncException::getUser
     */
    public function testGetUser()
    {
        $this->entityGetTest('user', 'User');
    }
}
