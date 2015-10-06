<?php
namespace Ilios\CoreBundle\EventListener;

use Doctrine\Common\Persistence\Event\PreUpdateEventArgs;
use Doctrine\ORM\Event\OnFlushEventArgs;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManagerInterface;
use Ilios\CoreBundle\Entity\Manager\AlertManagerInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * Creates or updates alerts on offering-creation/update.
 *
 * Class OfferingAlertListener
 * @package Ilios\CoreBundle\EventListener
 */
class OfferingAlertListener
{

    /**
     * @var UserInterface $user
     */
    protected $user;

    /**
     * @var AlertManagerInterface $manager
     */
    protected $alertManager;

    /**
     * @var AlertChangeTypeManagerInterface
     */
    protected $alertChangeTypeManager;

    /**
     * Set the username from injected security context
     * @param TokenStorageInterface $securityTokenStorage
     * @param AlertManagerInterface $alertManager
     * @param AlertChangeTypeManagerInterface $alertChangeTypeManager
     */
    public function __construct(
        TokenStorageInterface $securityTokenStorage,
        AlertManagerInterface $alertManager,
        AlertChangeTypeManagerInterface $alertChangeTypeManager
    ) {
        if (null !== $securityTokenStorage &&
            null !== $securityTokenStorage->getToken()
        ) {
            $this->user = $securityTokenStorage->getToken()->getUser();
        }
        $this->alertManager = $alertManager;
        $this->alertChangeTypeManager = $alertChangeTypeManager;
    }

    /**
     * @param PreUpdateEventArgs $eventArgs
     */
    public function preUpdate(PreUpdateEventArgs $eventArgs)
    {
        $entity = $eventArgs->getObject();

        if ($entity instanceof OfferingInterface) {
            /** @var OfferingInterface $entity */
            $entityChangeSet = $eventArgs->getEntityChangeSet();

            if (empty($entityChangeSet)) {
                return;
            }

            $map = [
                'learnerGroup' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP,
                'room' => AlertChangeTypeInterface::CHANGE_TYPE_LOCATION,
                'startDate' => AlertChangeTypeInterface::CHANGE_TYPE_TIME,
                'endDate' => AlertChangeTypeInterface::CHANGE_TYPE_TIME,
                'instructors' => AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR,
                'instructorGroups' => AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR,
                'learnerGroups' => AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP,
            ];

            $alert = $this->alertManager->findAlertBy([
                'dispatched' => false,
                'tableName' => 'offering',
                'tableRowId' => $entity->getId(),
            ]);

            if ($alert) {
                if (! $alert->getInstigators()->contains($this->user)) {
                    $alert->addInstigator($this->user);
                }
            } else {
                $alert = $this->alertManager->createAlert();
                $alert->setTableRowId($entity->getId());
                $alert->setTableName('offering');
                $alert->addInstigator($this->user);
                $alert->addRecipient($entity->getSession()->getCourse()->getSchool());
            }

            foreach ($entityChangeSet as $change) {
                if (array_key_exists($change, $map)) {
                    $changeType = $this->alertChangeTypeManager->findAlertChangeTypeBy([
                        'id' => $map[$change]
                    ]);
                    if (! $alert->getChangeTypes()->contains($changeType)) {
                        $alert->addChangeType($changeType);
                    }
                }
            }

            $this->alertManager->updateAlert($alert, false);
        }
    }

    /**
     * @param OnFlushEventArgs $eventArgs
     */
    public function onFlush(OnFlushEventArgs $eventArgs)
    {
        $entityManager = $eventArgs->getEntityManager();
        $uow = $entityManager->getUnitOfWork();
        $entities = $uow->getScheduledEntityInsertions();

        $alertMetadata = $entityManager->getClassMetadata('IliosCoreBundle:Alert');
        foreach ($entities as $entity) {
            if ($entity instanceof OfferingInterface) {
                /** @var OfferingInterface $entity */
                $alert = $this->alertManager->createAlert();
                $alert->setTableRowId($entity->getId());
                $alert->setTableName('offering');
                $alert->addInstigator($this->user);
                $changeType = $this->alertChangeTypeManager->findAlertChangeTypeBy([
                        'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING
                ]);
                $alert->addChangeType($changeType);
                $alert->addRecipient($entity->getSession()->getCourse()->getSchool());
                $this->alertManager->updateAlert($alert, false);
                $uow->computeChangeSet($alertMetadata, $alert);
            }
        }
    }
}
