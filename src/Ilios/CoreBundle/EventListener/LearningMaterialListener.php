<?php

namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\EventSubscriber;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;

use Ilios\CoreBundle\Model\LearningMaterials\FileInterface;

class LearningMaterialListener implements EventSubscriber
{
    public function getSubscribedEvents()
    {
        return [
            'prePersist'
        ];
    }

    public function prePersist(LifecycleEventArgs $args)
    {
        $object = $args->getObject();
        if ($object instanceof FileInterface) {
            $object->upload();
        }
    }
}
