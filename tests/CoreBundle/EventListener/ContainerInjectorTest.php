<?php
namespace Tests\CoreBundle\EventListener;

use Symfony\Bundle\FrameworkBundle\Tests\TestCase;
use Mockery as m;

use Ilios\CoreBundle\EventListener\ContainerInjector;

class ContainerInjectorTest extends TestCase
{
    public function tearDown()
    {
        m::close();
    }
    
    /**
     *
     * @var ContainerInjector
     */
    private $containerInjector;
    
    private $container;
    
    public function setUp()
    {
        $this->containerInjector = new ContainerInjector();
        $this->container = m::mock('Symfony\Component\DependencyInjection\ContainerInterface');
        $this->containerInjector->setContainer($this->container);
    }

    public function testNotAware()
    {
        $entity = m::mock('stdClass');
        $lifecycle = m::mock('Doctrine\ORM\Event\LifecycleEventArgs')
            ->shouldReceive('getEntity')
            ->times(1)->andReturn($entity)->getMock();
        $this->containerInjector->postLoad($lifecycle);
    }

    public function testAware()
    {
        $entity = m::mock('Symfony\Component\DependencyInjection\ContainerAwareInterface')
            ->shouldReceive('setContainer')->times(1)
            ->with($this->container)->getMock();
        $lifecycle = m::mock('Doctrine\ORM\Event\LifecycleEventArgs')
            ->shouldReceive('getEntity')->times(1)->andReturn($entity)
            ->getMock();
        $this->containerInjector->postLoad($lifecycle);
    }
}
