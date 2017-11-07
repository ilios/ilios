<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\AuthenticationBundle\Classes\SessionUserInterface;
use Ilios\CoreBundle\Entity\OfferingInterface;
use Ilios\CoreBundle\Entity\SessionInterface;
use Ilios\CoreBundle\Entity\UserInterface;
use Ilios\CoreBundle\Service\ChangeAlertHandler;
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
     * @var ChangeAlertHandler
     */
    protected $alertHandler;

    /**
     * Register injections here so we don't have to override the ApiController constructor
     *
     * @required
     * @param ChangeAlertHandler $alertHandler
     */
    public function setup(ChangeAlertHandler $alertHandler)
    {
        $this->alertHandler = $alertHandler;
    }

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
        /** @var SessionUserInterface $sessionUser */
        $userManager = $this->getManager('users');
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        /** @var UserInterface $user */
        $instigator = $userManager->findOneBy(['id' => $sessionUser->getId()]);

        $this->alertHandler->createAlertForNewOffering($offering, $instigator);
    }

    /**
     * @param OfferingInterface $offering
     * @param array $originalProperties
     */
    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties
    ) {
        $userManager = $this->getManager('users');
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();
        /** @var UserInterface $instigator */
        $instigator = $userManager->findOneBy(['id' => $sessionUser->getId()]);
        $this->alertHandler->createOrUpdateAlertForUpdatedOffering($offering, $instigator, $originalProperties);
    }
}
