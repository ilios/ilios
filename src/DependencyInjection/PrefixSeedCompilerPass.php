<?php

declare(strict_types=1);

namespace App\DependencyInjection;

use Composer\InstalledVersions;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Add the contents of our VERSION file to the cache prefix seed
 * this ensures that we won't have collisions in a production environment
 * when doing a blue/green deploy.
 */
class PrefixSeedCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        $seed = $container->getParameterBag()->resolveValue($container->getParameter('cache.prefix.seed'));
        $container->setParameter(
            'cache.prefix.seed',
            $seed . InstalledVersions::getPrettyVersion(InstalledVersions::getRootPackage()['name'])
        );
    }
}
