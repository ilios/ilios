<?php

namespace Ilios\LegacyCIBundle\Tests\Authentication;

use Ilios\LegacyCIBundle\Authentication\Factory;
use Ilios\LegacyCIBundle\Tests\TestCase;
use Mockery as m;

class FactoryTest extends TestCase
{

    /**
     * @var Factory
     */
    protected $object;

    protected function setUp()
    {
        $this->object = new Factory;
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Factory::create
     */
    public function testCreate()
    {
        $id = 13;
        $userProvider = 'fooprovider';
        $defaultEntryPoint = 'fooentrypoint';
        $definition = m::mock('Symfony\Component\DependencyInjection\DefinitionDecorator')
                ->shouldReceive('replaceArgument')->once()
                ->getMock();
        $container = m::mock('Symfony\Component\DependencyInjection\ContainerBuilder')
                ->shouldReceive('setDefinition')->times(2)
                ->andReturn($definition)
                ->getMock();
        $array = $this->object->create($container, $id, false, $userProvider, $defaultEntryPoint);

        $providerId = 'security.authentication.provider.ilios_legacy_ci.' . $id;
        $listenerId = 'security.authentication.listener.ilios_legacy_ci.' . $id;

        $this->assertArrayHasKey(0, $array);
        $this->assertArrayHasKey(1, $array);
        $this->assertArrayHasKey(2, $array);

        $this->assertSame($providerId, $array[0]);
        $this->assertSame($listenerId, $array[1]);
        $this->assertSame($defaultEntryPoint, $array[2]);
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Factory::getPosition
     */
    public function testGetPosition()
    {
        $this->assertSame('pre_auth', $this->object->getPosition());
    }

    /**
     * @covers Ilios\LegacyCIBundle\Authentication\Factory::getKey
     */
    public function testGetKey()
    {
        $this->assertSame('ilios_legacy_ci', $this->object->getKey());
    }
}
