<?php
namespace Tests\AppBundle\Entity;

use AppBundle\Entity\Authentication;
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
     * @covers \AppBundle\Entity\Authentication::setUsername
     * @covers \AppBundle\Entity\Authentication::getUsername
     */
    public function testSetUsername()
    {
        $this->basicSetTest('username', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Authentication::setPasswordSha256
     */
    public function testSetPasswordSha256()
    {
        $this->basicSetTest('passwordSha256', 'string');
    }

    /**
     * @covers \AppBundle\Entity\Authentication::setUser
     * @covers \AppBundle\Entity\Authentication::getUser
     */
    public function testSetUser()
    {
        $this->entitySetTest('user', 'User');
    }

    /**
     * @covers \AppBundle\Entity\Authentication::setInvalidateTokenIssuedBefore
     * @covers \AppBundle\Entity\Authentication::getInvalidateTokenIssuedBefore
     */
    public function testSetInvalidateTokenIssuedBefore()
    {
        $this->basicSetTest('invalidateTokenIssuedBefore', 'datetime');
    }
}
