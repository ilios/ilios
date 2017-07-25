<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UsersController
 * We have to handle a special 'q' parameter
 * as well as special handling for ICS feed keys
 * so users needs its own controller
 */
class UsersController extends ApiController
{
    /**
     * Handle special 'q' parameter in the request
     * @inheritdoc
     */
    public function getAllAction($version, $object, Request $request)
    {
        $q = $request->get('q');
        $parameters = $this->extractParameters($request);

        /** @var UserManager $manager */
        $manager = $this->getManager($object);

        if (null !== $q && '' !== $q) {
            $result = $manager->findUsersByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset'],
                $parameters['criteria']
            );

            return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
        }

        return parent::getAllAction($version, $object, $request);
    }

    /**
     * When Users are submitted with an empty icsFeedKey value that overrides
     * the created key.  This happens when new users are created and they don't have a
     * key yet.  Instead of using the blank key we need to keep the one that is generated
     * in the User entity constructor.
     *
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $data = $this->extractPostDataFromRequest($request, $object);
        $dataWithoutEmptyIcsFeed = array_map(function ($obj) {
            if (is_object($obj) && property_exists($obj, 'icsFeedKey')) {
                if (empty($obj->icsFeedKey)) {
                    unset($obj->icsFeedKey);
                }
            }

            return $obj;
        }, $data);
        $json = json_encode($dataWithoutEmptyIcsFeed);
        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        $obj = $this->extractPutDataFromRequest($request, $object);
        /*
                 Only a root user can make other users root.
                 This has to be done here because by the time it reaches the voter the
                 current user object in the session has been modified
               */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        if ($obj->root &&
            (!$currentUser->isRoot() && !$entity->isRoot())
        ) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }
        $json = json_encode($obj);

        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }
}
