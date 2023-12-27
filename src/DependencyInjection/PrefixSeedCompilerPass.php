<?php

declare(strict_types=1);

namespace App\DependencyInjection;

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
        $pathToVersionFile = __DIR__ . '/../../VERSION';
        if (is_readable($pathToVersionFile)) {
            $seed .= file_get_contents($pathToVersionFile);
        }
        $container->setParameter('cache.prefix.seed', $seed);
    }
}
