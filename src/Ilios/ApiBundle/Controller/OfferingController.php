<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\AlertChangeTypeInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class OfferingController
 *
 * When an offering is created or modified alerts get
 * recorded to let users know about the change
 *
 */
class OfferingController extends ApiController
{
    /**
     * Create alerts when adding offerings
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $json = $this->extractJsonFromRequest($request, $object, 'POST');
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flush();

        /** @var OfferingInterface $entity */
        foreach ($entities as $entity) {
            $session = $entity->getSession();
            if ($session && $session->isPublished()) {
                $this->createAlertForNewOffering($entity);
            }
        }

        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * Create alerts when modifying offerings
     * @inheritdoc
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        /** @var OfferingInterface $entity */
        $entity = $manager->findOneBy(['id'=> $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        $json = $this->extractJsonFromRequest($request, $object, 'PUT');
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, false);

        /** @var SessionInterface $session */
        $session = $entity->getSession();
        if ($session && $session->isPublished()) {
            if (Response::HTTP_CREATED === $code) {
                $this->createAlertForNewOffering($entity);
            } else {
                $this->createOrUpdateAlertForUpdatedOffering(
                    $entity,
                    $alertProperties
                );
            }
        }

        $manager->flush();

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    protected function extractParameters(Request $request)
    {
        $parameters = parent::extractParameters($request);
        if (array_key_exists('startDate', $parameters['criteria'])) {
            $parameters['criteria']['startDate'] = new \DateTime($parameters['criteria']['startDate']);
        }
        if (array_key_exists('endDate', $parameters['criteria'])) {
            $parameters['criteria']['endDate'] = new \DateTime($parameters['criteria']['endDate']);
        }
        if (array_key_exists('updatedAt', $parameters['criteria'])) {
            $parameters['criteria']['updatedAt'] = new \DateTime($parameters['criteria']['updatedAt']);
        }

        return $parameters;
    }

    /**
     * @param OfferingInterface $offering
     */
    protected function createAlertForNewOffering(OfferingInterface $offering)
    {
        // create new alert for this offering
        $alertManager = $this->getManager('alerts');
        $userManager = $this->getManager('users');
        $alertChangeTypeManager = $this->getManager('alertchangetypes');
        $alert = $alertManager->create();
        $alert->addChangeType($alertChangeTypeManager->findOneBy([
            'id' => AlertChangeTypeInterface::CHANGE_TYPE_NEW_OFFERING]));
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        $user = $userManager->findOneBy(['id' => $sessionUser->getId()]);
        $alert->addInstigator($user);
        $alert->addRecipient($offering->getSession()->getCourse()->getSchool());
        $alert->setTableName('offering');
        $alert->setTableRowId($offering->getId());
        $alertManager->update($alert, false);
    }

    /**
     * @param OfferingInterface $offering
     * @param array $originalProperties
     */
    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
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

        $alertManager = $this->getManager('alerts');
        $alertChangeTypeManager = $this->getManager('alertchangetypes');

        $alert = $alertManager->findOneBy([
            'dispatched' => false,
            'tableName' => 'offering',
            'tableRowId' => $offering->getId()
        ]);

        if (! $alert) {
            $recipient = $offering->getSchool();
            if (! $recipient) {
                return; // SOL.
            }
            $alert = $alertManager->create();
            $alert->addRecipient($recipient);
            $alert->setTableName('offering');
            $alert->setTableRowId($offering->getId());

            $userManager = $this->getManager('users');
            /** @var SessionUserInterface $sessionUser */
            $sessionUser = $this->tokenStorage->getToken()->getUser();
            $user = $userManager->findOneBy(['id' => $sessionUser->getId()]);
            $alert->addInstigator($user);
        }

        foreach ($changeTypes as $type) {
            $changeType = $alertChangeTypeManager->findOneBy(['id' => $type]);
            if ($changeType && ! $alert->getChangeTypes()->contains($changeType)) {
                $alert->addChangeType($changeType);
            }
        }

        $alertManager->update($alert, false);
    }
}
