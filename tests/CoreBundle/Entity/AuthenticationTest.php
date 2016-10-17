<?php
namespace Tests\CoreBundle\Entity;

use Ilios\CoreBundle\Entity\Authentication;
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
     * @covers \Ilios\CoreBundle\Entity\Authentication::setUsername
     * @covers \Ilios\CoreBundle\Entity\Authentication::getUsername
     */
    public function testSetUsername()
    {
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Authentication::setPasswordSha256
     */
    public function testSetPasswordSha256()
    {
        $this->basicSetTest('passwordSha256', 'string');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Authentication::setUser
     * @covers \Ilios\CoreBundle\Entity\Authentication::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \Ilios\CoreBundle\Entity\Authentication::setInvalidateTokenIssuedBefore
     * @covers \Ilios\CoreBundle\Entity\Authentication::getInvalidateTokenIssuedBefore
     */
    public function testSetInvalidateTokenIssuedBefore()
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }
}
