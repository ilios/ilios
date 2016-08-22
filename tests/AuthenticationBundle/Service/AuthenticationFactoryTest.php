<?php
namespace Tests\AuthenticationBundle\Service;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

use Ilios\AuthenticationBundle\Service\AuthenticationFactory;

class AuthenticationFactoryTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }

    public function testConstructor()
    {
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $obj = new AuthenticationFactory(
            $container,
            'test-service'
        );
        $this->assertTrue($obj instanceof AuthenticationFactory);
    }

    public function testCreateService()
    {
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $obj = new AuthenticationFactory(
            $container,
            'test-service'
        );
        $container->shouldReceive('get')->with('test-service')->andReturn(42);
        
        $service = $obj->createAuthenticationService();
        $this->assertSame(42, $service);
    }
}
