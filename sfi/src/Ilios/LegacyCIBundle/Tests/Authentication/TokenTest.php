<?php

namespace Ilios\LegacyCIBundle\Tests\Authentication;

use Ilios\LegacyCIBundle\Authentication\Token;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

class TokenTest extends TestCase
{

    /**
     * @var CIToken
     */
    protected $token;

    /**
     *  Instantiate a Token and service dependencies
     */
    protected function setUp()
    {
        $handler = m::mock('Ilios\LegacyCIBundle\Session\Handler');
        $handler->shouldReceive('getUserId')->times(1)->andReturn('testusername');
        $this->token = new Token($handler);
    }

    public function testAuthenticated()
    {
        $this->assertTrue($this->token->isAuthenticated());
    }

    public function testNotAuthenticated()
    {
        $handler = m::mock('Ilios\LegacyCIBundle\Session\Handler');
        $handler->shouldReceive('getUserId')->times(1)->andReturn(false);
        $token = new Token($handler);
        $this->assertFalse($token->isAuthenticated());
    }

    public function testGetRoles()
    {
        $this->assertSame(array(), $this->token->getRoles());
    }

    public function testAuthenticatedGetRoles()
    {
        $roles = array('one', 'two', 'three');
        $user = m::mock('Symfony\Component\Security\Core\User\UserInterface')
                ->shouldReceive('getRoles')->once()->andReturn($roles)
                ->getMock();
        $this->token->setUser($user);
        $tokenRoles = array();
        foreach ($this->token->getRoles() as $role) {
            $tokenRoles[] = $role->getRole();
        }
        foreach ($roles as $role) {
            $this->assertTrue(in_array($role, $tokenRoles));
        }
    }

    public function testGetCredentials()
    {
        $this->assertSame('', $this->token->getCredentials());
    }

    public function testGetUser()
    {
        $this->assertSame('testusername', $this->token->getUser());
    }

    public function testSetUser()
    {
        $this->token->setUser('newusername');
        $this->assertSame('newusername', $this->token->getUser());
    }

    public function testSerializerNew()
    {
        $serialized = $this->token->serialize();
        $handler = m::mock('Ilios\LegacyCIBundle\Session\Handler');
        $handler->shouldReceive('getUserId')->times(1)->andReturn('testusername');
        $newToken = new Token($handler);
        $newToken->unserialize($serialized);
        $this->assertSame($newToken->getRoles(), $this->token->getRoles());
        $this->assertSame($newToken->getUser(), $this->token->getUser());
        $this->assertSame($newToken->getAttributes(), $this->token->getAttributes());
    }

    public function testSerializerWithUser()
    {
        $roles = array('one', 'two', 'three');
        $user = m::mock('Symfony\Component\Security\Core\User\UserInterface')
                ->shouldReceive('getRoles')->once()->andReturn($roles)
                ->getMock();
        $this->token->setUser($user);
        $serialized = $this->token->serialize();
        $handler = m::mock('Ilios\LegacyCIBundle\Session\Handler');
        $handler->shouldReceive('getUserId')->times(1)->andReturn('testusername');
        $newToken = new Token($handler);
        $newToken->unserialize($serialized);
        
        $tokenRoles = array();
        foreach ($newToken->getRoles() as $role) {
            $tokenRoles[] = $role->getRole();
        }
        foreach ($roles as $role) {
            $this->assertTrue(in_array($role, $tokenRoles));
        }
    }

    public function testGetUserName()
    {
        $this->assertSame('testusername', $this->token->getUser());
    }
}
