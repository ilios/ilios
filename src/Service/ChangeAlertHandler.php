<?php

declare(strict_types=1);

namespace App\Service;

use App\Entity\AlertChangeTypeInterface;
use App\Entity\AlertInterface;
use App\Entity\OfferingInterface;
use App\Entity\ServiceTokenInterface;
use App\Entity\UserInterface;
use App\Repository\AlertChangeTypeRepository;
use App\Repository\AlertRepository;

/**
 * Creates and updates change alerts for given data points, such as offerings.
 *
 * Class ChangeAlertHandler
 * @package App\Service
 */
class ChangeAlertHandler
{
    public function __construct(
        protected AlertRepository $alertRepository,
        protected AlertChangeTypeRepository $alertChangeTypeRepository
    ) {
    }

    public function createAlertForNewOffering(
        OfferingInterface $offering,
        ?UserInterface $instigator = null,
        ?ServiceTokenInterface $serviceTokenInstigator = null
    ): void {
        // create new alert for this offering
        /** @var AlertInterface $alert */
        $alert = $this->alertRepository->create();
        $alert->addChangeType($this->alertChangeTypeRepository->findOneBy([
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING]));
        if ($instigator) {
            $alert->addInstigator($instigator);
        }
        if ($serviceTokenInstigator) {
            $alert->addServiceTokenInstigator($serviceTokenInstigator);
        }
        $alert->addRecipient($offering->getSession()->getCourse()->getSchool());
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());
        $this->alertRepository->update($alert, false);
    }

    public function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties,
        ?UserInterface $instigator = null,
        ?ServiceTokenInterface $serviceTokenInstigator = null,
    ): void {
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
        if ($updatedProperties['url'] !== $originalProperties['url']) {
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
        $changeTypes = array_unique($changeTypes);

        $alert = $this->alertRepository->findOneBy([
            'dispatched' => false,
            'tableName' => 'offering',
            'tableRowId' => $offering->getId(),
        ]);

        if (! $alert) {
            $recipient = $offering->getSchool();
            $alert = $this->alertRepository->create();
            $alert->addRecipient($recipient);
            $alert->setTableName('offering');
            $alert->setTableRowId($offering->getId());
            if ($instigator) {
                $alert->addInstigator($instigator);
            }
            if ($serviceTokenInstigator) {
                $alert->addServiceTokenInstigator($serviceTokenInstigator);
            }
        }

        foreach ($changeTypes as $type) {
            $changeType = $this->alertChangeTypeRepository->findOneBy(['id' => $type]);
            if ($changeType && ! $alert->getChangeTypes()->contains($changeType)) {
                $alert->addChangeType($changeType);
            }
        }

        $this->alertRepository->update($alert, false);
    }
}
