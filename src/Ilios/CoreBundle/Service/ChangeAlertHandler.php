<?php

namespace Ilios\CoreBundle\Service;

use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\Manager\AlertChangeTypeManager;
use Ilios\CoreBundle\Entity\Manager\AlertManager;
use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\UserInterface;

/**
 * Creates and updates change alerts for given data points, such as offerings.
 *
 * Class ChangeAlertHandler
 * @package Ilios\CoreBundle\Service
 */
class ChangeAlertHandler
{
    /**
     * @var AlertManager
     */
    protected $alertManager;

    /**
     * @var AlertChangeTypeManager
     */
    protected $alertChangeTypeManager;

    /**
     * @param AlertManager $alertManager
     * @param AlertChangeTypeManager $alertChangeTypeManager
     * @param UserManager $userManager
     */
    public function __construct(
        AlertManager $alertManager,
        AlertChangeTypeManager $alertChangeTypeManager,
        UserManager $userManager
    ) {
        $this->alertManager = $alertManager;
        $this->alertChangeTypeManager = $alertChangeTypeManager;
    }

    /**
     * @param OfferingInterface $offering
     * @param UserInterface $instigator
     */
    public function createAlertForNewOffering(OfferingInterface $offering, UserInterface $instigator)
    {
        // create new alert for this offering
        $alert = $this->alertManager->create();
        $alert->addChangeType($this->alertChangeTypeManager->findOneBy([
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING]));
        $alert->addInstigator($instigator);
        $alert->addRecipient($offering->getSession()->getCourse()->getSchool());
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());
        $this->alertManager->update($alert, false);
    }

    /**
     * @param OfferingInterface $offering
     * @param UserInterface $instigator
     * @param array $originalProperties
     */
    public function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        UserInterface $instigator,
        array $originalProperties
    ) {
        $updatedProperties = $offering->getAlertProperties();

        $changeTypes = [];
        if ($updatedProperties['startDate'] !== $originalProperties['startDate']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_TIME;
        }
        if ($updatedProperties['endDate'] !== $originalProperties['endDate']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_TIME;
        }
        if ($updatedProperties['room'] !== $originalProperties['room']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LOCATION;
        }
        if ($updatedProperties['site'] !== $originalProperties['site']) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LOCATION;
        }
        $instructorIdsDiff = array_merge(
            array_diff($updatedProperties['instructors'], $originalProperties['instructors']),
            array_diff($originalProperties['instructors'], $updatedProperties['instructors'])
        );
        if (! empty($instructorIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR;
        }
        $instructorGroupIdsDiff = array_merge(
            array_diff($updatedProperties['instructorGroups'], $originalProperties['instructorGroups']),
            array_diff($originalProperties['instructorGroups'], $updatedProperties['instructorGroups'])
        );
        if (! empty($instructorGroupIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_INSTRUCTOR;
        }
        $learnerIdsDiff = array_merge(
            array_diff($updatedProperties['learners'], $originalProperties['learners']),
            array_diff($originalProperties['learners'], $updatedProperties['learners'])
        );
        if (! empty($learnerIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP;
        }
        $learnerGroupIdsDiff = array_merge(
            array_diff($updatedProperties['learnerGroups'], $originalProperties['learnerGroups']),
            array_diff($originalProperties['learnerGroups'], $updatedProperties['learnerGroups'])
        );
        if (! empty($learnerGroupIdsDiff)) {
            $changeTypes[] = AlertChangeTypeInterface::CHANGE_TYPE_LEARNER_GROUP;
        }

        if (empty($changeTypes)) {
            return;
        }
        array_unique($changeTypes);

        $alert = $this->alertManager->findOneBy([
            'dispatched' => false,
            'tableName' => 'offering',
            'tableRowId' => $offering->getId()
        ]);

        if (! $alert) {
            $recipient = $offering->getSchool();
            if (! $recipient) {
                return; // SOL.
            }
            $alert = $this->alertManager->create();
            $alert->addRecipient($recipient);
            $alert->setTableName('offering');
            $alert->setTableRowId($offering->getId());
            $alert->addInstigator($instigator);
        }

        foreach ($changeTypes as $type) {
            $changeType = $this->alertChangeTypeManager->findOneBy(['id' => $type]);
            if ($changeType && ! $alert->getChangeTypes()->contains($changeType)) {
                $alert->addChangeType($changeType);
            }
        }

        $this->alertManager->update($alert, false);
    }
}
