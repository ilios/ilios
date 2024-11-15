<?php

declare(strict_types=1);

namespace App\Tests\DependencyInjection;

use PHPUnit\Framework\Attributes\CoversClass;
use App\DependencyInjection\PrefixSeedCompilerPass;
use App\Tests\TestCase;
use Composer\InstalledVersions;
use Mockery as m;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

#[CoversClass(PrefixSeedCompilerPass::class)]
class PrefixSeedCompilerPassTest extends TestCase
{
    protected m\MockInterface $config;
    protected PrefixSeedCompilerPass $pass;

    protected function setUp(): void
    {
        parent::setUp();
        $this->pass = new PrefixSeedCompilerPass();
    }

    protected function tearDown(): void
    {
        parent::tearDown();
        unset($this->pass);
    }

    public function testProcess(): void
    {
        $container = new ContainerBuilder();
        $seed = 'seed123456';
        $iliosVersion = InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name']);
        $container->setParameter('cache.prefix.seed', $seed);
        $this->pass->process($container);
        $this->assertEquals(
            $seed . $iliosVersion,
            $container->getParameterBag()->resolveValue($container->getParameter('cache.prefix.seed'))
        );
    }
}
