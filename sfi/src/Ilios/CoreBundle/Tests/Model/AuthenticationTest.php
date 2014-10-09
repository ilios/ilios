<?php
namespace Ilios\CoreBundle\Tests\Model;


use Ilios\CoreBundle\Model\Authentication;
use Mockery as m;

/**
 * Tests for Model Authentication
 */
class AuthenticationTest extends ModelBase
{
    /**
     * @var Authentication
     */
    protected $object;

    /**
     * Instantiate a Authentication object
     */
    protected function setUp()
    {
        $this->object = new Authentication;
    }
    

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::setPersonId
     */
    public function testSetPersonId()
    {
        $this->basicSetTest('personId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::getPersonId
     */
    public function testGetPersonId()
    {
        $this->basicGetTest('personId', 'integer');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::setUsername
     */
    public function testSetUsername()
    {
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::getUsername
     */
    public function testGetUsername()
    {
        $this->basicGetTest('username', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::setPasswordSha256
     */
    public function testSetPasswordSha256()
    {
        $this->basicSetTest('passwordSha256', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::getPasswordSha256
     */
    public function testGetPasswordSha256()
    {
        $this->basicGetTest('passwordSha256', 'string');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::setUser
     */
    public function testSetUser()
    {
        $this->modelSetTest('user', 'User');
    }

    /**
     * @covers Ilios\CoreBundle\Model\Authentication::getUser
     */
    public function testGetUser()
    {
        $this->modelGetTest('user', 'User');
    }
}
