<?php

declare(strict_types=1);

namespace App\EventListener;

use DateTime;
use App\Service\Timestamper;
use App\Traits\TimestampableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Entity\SessionStampableInterface;
use Doctrine\ORM\Event\PostPersistEventArgs;
use Doctrine\ORM\Event\PostRemoveEventArgs;
use Doctrine\ORM\Event\PostUpdateEventArgs;
use Exception;

/**
 * Doctrine event listener.
 * Listen for every change to an entity and timestamp it if appropriate.
 *
 * Class TimestampEntityChanges
 */
class TimestampEntityChanges
{
    /**
     * TimestampEntityChanges constructor.
     */
    public function __construct(protected Timestamper $timeStamper)
    {
    }

    /**
     * @throws Exception
     */
    public function postUpdate(PostUpdateEventArgs $args): void
    {
        $this->stamp($args->getObject());
    }

    /**
     * @throws Exception
     */
    public function postRemove(PostRemoveEventArgs $args): void
    {
        $this->stamp($args->getObject());
    }

    /**
     * @throws Exception
     */
    public function postPersist(PostPersistEventArgs $args): void
    {
        $this->stamp($args->getObject());
    }

    /**
     * @throws Exception
     */
    protected function stamp(object $entity): void
    {
        $now = DateTime::createFromFormat('U', (string) time());

        if ($entity instanceof TimestampableEntityInterface) {
            $this->timeStamper->add($entity, $now);
            $entity->setUpdatedAt($now);
        }

        if ($entity instanceof OfferingsEntityInterface) {
            $offerings = $entity->getOfferings();
            foreach ($offerings as $offering) {
                $this->timeStamper->add($offering, $now);
                $offering->setUpdatedAt($now);
            }
        }

        if ($entity instanceof SessionStampableInterface) {
            $sessions = $entity->getSessions();
            foreach ($sessions as $session) {
                $this->timeStamper->add($session, $now);
                $session->setUpdatedAt($now);
            }
        }
    }
}
