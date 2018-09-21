<?php
namespace App\Tests\Entity;

use App\Entity\Authentication;
use Mockery as m;

/**
 * Tests for Entity Authentication
 */
class AuthenticationTest extends EntityBase
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
     * @covers \App\Entity\Authentication::setUsername
     * @covers \App\Entity\Authentication::getUsername
     */
    public function testSetUsername()
    {
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::setPasswordSha256
     */
    public function testSetPasswordSha256()
    {
        $this->basicSetTest('passwordSha256', 'string');
    }

    /**
     * @covers \App\Entity\Authentication::setUser
     * @covers \App\Entity\Authentication::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \App\Entity\Authentication::setInvalidateTokenIssuedBefore
     * @covers \App\Entity\Authentication::getInvalidateTokenIssuedBefore
     */
    public function testSetInvalidateTokenIssuedBefore()
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }
}
