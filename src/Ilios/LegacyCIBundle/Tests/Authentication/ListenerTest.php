<?php

namespace Ilios\LegacyCIBundle\Tests\Authentication;

use Ilios\LegacyCIBundle\Authentication\Listener;
use Symfony\Component\Security\Core\Exception\AuthenticationException;
use Symfony\Component\HttpFoundation\Response;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

class ListenerTest extends TestCase
{

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Listener::__construct
     * @covers Ilios\LegacyCIBundle\Authentication\Listener::handle
     */
    public function testHandle()
    {
        $token = m::mock('Ilios\LegacyCIBundle\Authentication\Token');
        $securityContext = m::mock('Symfony\Component\Security\Core\SecurityContextInterface')
                ->shouldReceive('setToken')->with($token)
                ->getMock();

        $authenticationManager =
            m::mock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
            ->shouldReceive('authenticate')->with($token)->andReturn($token)
            ->getMock();


        $event = m::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent');

        $object = new Listener($securityContext, $authenticationManager, $token);
        $object->handle($event);
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Listener::handle
     */
    public function testHandleException()
    {
        $token = m::mock('Ilios\LegacyCIBundle\Authentication\Token');
        $securityContext = m::mock('Symfony\Component\Security\Core\SecurityContextInterface');

        $authenticationManager =
            m::mock('Symfony\Component\Security\Core\Authentication\AuthenticationManagerInterface')
            ->shouldReceive('authenticate')->with($token)
            ->andThrow(new AuthenticationException)
            ->getMock();


        $event = m::mock('Symfony\Component\HttpKernel\Event\GetResponseEvent')
            ->shouldReceive('setResponse')->once()->with(m::on(function ($response) {
                if (!$response instanceof Response) {
                    return false;
                }
                if ($response->getStatusCode() != Response::HTTP_FORBIDDEN) {
                    return false;
                }

                return true;
            }))->getMock();

        $object = new Listener($securityContext, $authenticationManager, $token);
        $object->handle($event);
    }
}
