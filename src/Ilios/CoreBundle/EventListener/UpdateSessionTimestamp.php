<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Ilios\CoreBundle\Entity\Session;

/**
 * Doctrine event listener.
 *
 * To correctly set the session last_updated timestamp we have to listen for updates to the session as well as
 * all the related entities.
 *
 * The Doctrine built in LifeCycle Callbacks were not able to handle this correctly,
 * or else I was never able to write them correctly.
 *
 * Class UpdateSessionTimestamp
 * @package Ilios\CoreBundle\EventListener
 */
class UpdateSessionTimestamp
{
    /**
    * Grab all of the entities that have a relationship with session and update the session
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
        $sessionMetadata = $entityManager->getClassMetadata('IliosCoreBundle:Session');
        $sessions = [];
        foreach ($entities as $entity) {
            switch (get_class($entity)) {
                case 'Ilios\CoreBundle\Entity\IlmSession':
                    $sessions[] = $entity->getSession();
                    break;
                case 'Ilios\CoreBundle\Entity\LearningMaterial':
                    foreach ($entity->getSessionLearningMaterials() as $sessionLearningMaterial) {
                        $sessions[] = $sessionLearningMaterial->getSession();
                    }
                    break;
                case 'Ilios\CoreBundle\Entity\SessionLearningMaterial':
                    $sessions[] = $entity->getSession();
                    break;
                case 'Ilios\CoreBundle\Entity\SessionDescription':
                    $sessions[] = $entity->getSession();
                    break;
            }
        }
        $sessions = array_filter($sessions, function ($obj) {
            return !!$obj && $obj instanceof Session;
        });
        $sessions = array_unique($sessions);
        foreach ($sessions as $session) {
            if (!$uow->isScheduledForDelete($session)) {
                $session->stampUpdate();
                if ($uow->isScheduledForUpdate($session) or $uow->isScheduledForInsert($session)) {
                    $uow->recomputeSingleEntityChangeSet($sessionMetadata, $session);
                } else {
                    $entityManager->persist($session);
                    $uow->computeChangeSet($sessionMetadata, $session);
                }
            }
        }
    }
}
