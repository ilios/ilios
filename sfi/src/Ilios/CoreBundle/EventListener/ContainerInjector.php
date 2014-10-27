<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\LifecycleEventArgs;
use Symfony\Component\DependencyInjection\ContainerAware;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;

/**
 * @todo: Why is this here? Which entities require the container?
 * Class ContainerInjector
 * @package Ilios\CoreBundle\EventListener
 */
class ContainerInjector extends ContainerAware
{
    public function postLoad(LifecycleEventArgs $args)
    {
        $entity = $args->getEntity();
        if ($entity instanceof ContainerAwareInterface) {
            $entity->setContainer($this->container);
        }
    }
}
