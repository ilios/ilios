<?php

namespace Ilios\AuthenticationBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Class IliosAuthenticationBundle
 * @package Ilios\AuthenticationBundle
 */
class IliosAuthenticationBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);
    }
}
