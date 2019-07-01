<?php
namespace App\EventListener;

use DateTime;
use Doctrine\Common\Persistence\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use App\Service\Timestamper;
use App\Traits\TimestampableEntityInterface;
use App\Traits\OfferingsEntityInterface;
use App\Entity\SessionStampableInterface;
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

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws Exception
     */
    public function postUpdate(LifecycleEventArgs $args)
    {
        $this->stamp($args->getObject(), new DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws Exception
     */
    public function postRemove(LifecycleEventArgs $args)
    {
        $this->stamp($args->getObject(), new DateTime());
    }

    /**
     * @param LifecycleEventArgs $args
     *
     * @throws Exception
     */
    public function postPersist(LifecycleEventArgs $args)
    {
        $this->stamp($args->getObject(), new DateTime());
    }

    /**
     * @param $entity
     * @param DateTime $timestamp
     *
     * @throws Exception
     */
    protected function stamp($entity, Datetime $timestamp)
    {
        if ($entity instanceof TimestampableEntityInterface) {
            $this->timeStamper->add($entity, $timestamp);
            $entity->setUpdatedAt($timestamp);
        }

        if ($entity instanceof OfferingsEntityInterface) {
            $offerings = $entity->getOfferings();
            foreach ($offerings as $offering) {
                $this->timeStamper->add($offering, $timestamp);
                $offering->setUpdatedAt($timestamp);
            }
        }

        if ($entity instanceof SessionStampableInterface) {
            $sessions = $entity->getSessions();
            foreach ($sessions as $session) {
                $this->timeStamper->add($session, $timestamp);
                $session->setUpdatedAt($timestamp);
            }
        }
    }
}
