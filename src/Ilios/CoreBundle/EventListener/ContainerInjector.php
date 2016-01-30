<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerAwareTrait;

/**
 * Class ContainerInjector
 * @package Ilios\CoreBundle\EventListener
 */
class ContainerInjector
{
    use ContainerAwareTrait;

    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof ContainerAwareInterface) {
            $entity->setContainer($this->container);
        }
    }
}
