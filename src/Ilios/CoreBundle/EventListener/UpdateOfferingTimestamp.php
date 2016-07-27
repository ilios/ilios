<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;

use Ilios\CoreBundle\Entity\Offering;

/**
 * Doctrine event listener.
 *
 * To correctly set the offering last_updated timestamp we have to listen for updates to the offering as well as
 * all the related entities.
 *
 * The Doctrine built in LifeCycle Callbacks were not able to handle this correctly,
 * or else I was never able to write them correctly.
 *
 * Class UpdateOfferingTimestamp
 * @package Ilios\CoreBundle\EventListener
 */
class UpdateOfferingTimestamp
{
    /**
    * Grab all of the entities that have a relationship with offering and update the offering
    * they are associated with.
    *
    * We have to do this operation using onFlush so we can catch inserts, updated
    * and deletes for all associations.
    *
    * @param OnFlushEventArgs $eventArgs
    */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );
        $offeringMetadata = $entityManager->getClassMetadata('IliosCoreBundle:Offering');
        $offerings = [];
        foreach ($entities as $entity) {
            switch (get_class($entity)) {
                case 'Ilios\CoreBundle\Entity\LearnerGroup':
                case 'Ilios\CoreBundle\Entity\InstructorGroup':
                    foreach ($entity->getOfferings() as $offering) {
                        $offerings[] = $offering;
                    }
                    break;
            }
        }
        $offerings = array_filter($offerings, function ($obj) {
            return !!$obj && $obj instanceof Offering;
        });
        $offerings = array_unique($offerings);
        foreach ($offerings as $offering) {
            if (!$uow->isScheduledForDelete($offering)) {
                $offering->stampUpdate();
                if ($uow->isScheduledForUpdate($offering) or $uow->isScheduledForInsert($offering)) {
                    $uow->recomputeSingleEntityChangeSet($offeringMetadata, $offering);
                } else {
                    $entityManager->persist($offering);
                    $uow->computeChangeSet($offeringMetadata, $offering);
                }
            }
        }
    }
}
