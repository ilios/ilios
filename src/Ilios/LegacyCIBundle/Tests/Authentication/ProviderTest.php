<?php

namespace Ilios\LegacyCIBundle\Tests\Authentication;

use Ilios\LegacyCIBundle\Authentication\Provider;
use Ilios\LegacyCIBundle\Authentication\Token;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

class ProviderTest extends TestCase
{

    /**
     *
     * @var Ilios\LegacyCIBundle\Authentication\Provider
     */
    protected $provider;
    protected $userProvider;

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Provider::__construct
     */
    public function setUp()
    {
        $this->userProvider = m::mock('Symfony\Component\Security\Core\User\UserProviderInterface');
        $this->provider = new Provider($this->userProvider);
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Provider::supports
     */
    public function testSupports()
    {
        $token1 = m::mock('Ilios\LegacyCIBundle\Authentication\Token');
        $this->assertTrue($this->provider->supports($token1));

        $token3 = m::mock('Symfony\Component\Security\Core\Authentication\Token\TokenInterface');
        $this->assertFalse($this->provider->supports($token3));
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Provider::authenticate
     */
    public function testGoodUser()
    {
        $roles = array('one', 'two');
        $user = m::mock('Symfony\Component\Security\Core\User\UserInterface')
                ->shouldReceive('getRoles')->andReturn($roles)
                ->getMock();
        $token = m::mock('Ilios\LegacyCIBundle\Authentication\Token')
                ->shouldReceive('getUsername')->andReturn('userName')
                ->shouldReceive('setUser')->with($user)->once()
                ->shouldReceive('setAuthenticated')->with(true)->once()
                ->getMock();
        $this->userProvider->shouldReceive('loadUserByUsername')->with('userName')
                ->andReturn($user);

        $returnToken = $this->provider->authenticate($token);
        $this->assertSame($returnToken, $token);
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Provider::authenticate
     */
    public function testBadUser()
    {
        $this->setExpectedException('Symfony\Component\Security\Core\Exception\AuthenticationException');
        $token = m::mock('Ilios\LegacyCIBundle\Authentication\Token')
                ->shouldReceive('getUsername')->andReturn('badUserName')
                ->getMock();

        $this->userProvider->shouldReceive('loadUserByUsername')->with('badUserName')
                ->andReturn(false);

        $this->provider->authenticate($token);
    }
}
