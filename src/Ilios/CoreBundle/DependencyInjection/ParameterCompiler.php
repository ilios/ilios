<?php

namespace Ilios\CoreBundle\DependencyInjection;

use Ilios\CoreBundle\Service\Config;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class ParameterCompiler
 *
 * Some of our configuration options are stored in the database but still
 * need to be exposed to third-party services.
 * This ParameterCompiler pulls those values out of the database and injects them into the
 * service by replacing the arguments
 *
 * @package Ilios\CoreBundle\DependencyInjection
 */
class ParameterCompiler implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $config = $container->get(Config::class);

        $trackingCode = $config->get('tracking_code');
        $trackerDef = $container->getDefinition('happyr.google_analytics.tracker');
        $arguments = $trackerDef->getArguments();
        $trackingIdArgument = array_search('INJECTED FROM DB IN ParameterCompiler', $arguments);
        $trackerDef->replaceArgument($trackingIdArgument, $trackingCode);
    }
}
