<?php
namespace Ilios\LegacyCIBundle\Tests;

use Ilios\LegacyCIBundle\IliosLegacyCIBundle;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

class IliosLegacyCIBundleTest extends TestCase
{
    public function testBuild()
    {
        $bundle = new IliosLegacyCIBundle();
        
        $extension = m::mock('Symfony\Bundle\SecurityBundle\DependencyInjection\SecurityExtension')
            ->shouldReceive('addSecurityListenerFactory')
            ->with(m::type('Ilios\LegacyCIBundle\Authentication\Factory'))
            ->getMock();
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder')
            ->shouldReceive('getExtension')->with('security')->once()->andReturn($extension)
            ->getMock();
        $bundle->build($container);
    }
}
