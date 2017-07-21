<?php
namespace Ilios\CoreBundle;

use Ilios\CoreBundle\DependencyInjection\ParameterCompiler;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class IliosCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new ParameterCompiler(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
