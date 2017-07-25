<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\ORM\Event\OnFlushEventArgs;
use Ilios\CoreBundle\Service\Timestamper;
use Ilios\CoreBundle\Traits\TimestampableEntityInterface;
use Ilios\CoreBundle\Traits\OfferingsEntityInterface;
use Ilios\CoreBundle\Entity\SessionStampableInterface;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and timestamp it if appropriate.
 *
 * Class TimestampEntityChanges
 */
class TimestampEntityChanges
{
    /**
     * @var Timestamper
     */
    protected $timeStamper;

    /**
     * TimestampEntityChanges constructor.
     * @param Timestamper $timeStamper
     */
    public function __construct(Timestamper $timeStamper)
    {
        $this->timeStamper = $timeStamper;
    }

    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $entities = array_merge(
            $uow->getScheduledEntityInsertions(),
            $uow->getScheduledEntityUpdates(),
            $uow->getScheduledEntityDeletions()
        );

        foreach ($entities as $entity) {
            $this->stamp($entity);
        }
    }

    protected function stamp($entity)
    {
        if ($entity instanceof TimestampableEntityInterface) {
            $this->timeStamper->add($entity);
        }

        if ($entity instanceof OfferingsEntityInterface) {
            $offerings = $entity->getOfferings();
            foreach ($offerings as $offering) {
                $this->timeStamper->add($offering);
            }
        }

        if ($entity instanceof SessionStampableInterface) {
            $sessions = $entity->getSessions();
            foreach ($sessions as $session) {
                $this->timeStamper->add($session);
            }
        }
    }
}
