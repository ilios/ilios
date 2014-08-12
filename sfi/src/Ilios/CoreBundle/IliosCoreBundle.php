<?php
namespace Ilios\CoreBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Ilios\CoreBundle\DependencyInjection\Compiler\ValidatorPass;

class IliosCoreBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
        $container->addCompilerPass(new ValidatorPass());
    }
}
