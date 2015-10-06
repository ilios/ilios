<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Service\AlertLogger;
use Symfony\Component\DependencyInjection\ContainerAware;


/**
 * Creates or updates alerts on offering-creation/update.
 *
 * Class OfferingAlertListener
 * @package Ilios\CoreBundle\EventListener
 */
class OfferingAlertListener extends ContainerAware
{
    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $alertLogger = $this->container->get('ilioscore.alertlogger');

        $entity = $eventArgs->getObject();

        $entityChangeSet = $eventArgs->getEntityChangeSet();
        if (empty($entityChangeSet)) {
            return;
        }
        if ($entity instanceof OfferingInterface) {
            $alertLogger->updateOfferingAlert($entity, $entityChangeSet);
        }
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $alertLogger = $this->container->get('ilioscore.alertlogger');

        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $entities = $uow->getScheduledEntityInsertions();

        $alertMetadata = $entityManager->getClassMetadata('IliosCoreBundle:Alert');
        foreach ($entities as $entity) {
            if ($entity instanceof OfferingInterface) {
                $alert = $alertLogger->addOfferingAlert($entity);
                $uow->computeChangeSet($alertMetadata, $alert);
            }
        }
    }
}
