<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\UserManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class UsersController
 * We have to handle a special 'q' parameter on users
 * so it needs its own controller
 * @package Ilios\ApiBundle\Controller
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

        if (null !== $q) {
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

        $obj = $this->extractDataFromRequest($request, $object, $singleItem = true, $returnObject = true);
        /*
            Only a root user can make other users root.
            This has to be done here because by the time it reaches the voter the
            current user object in the session has been modified
        */
        $currentUser = $this->get('security.token_storage')->getToken()->getUser();
        if (
            $obj->root &&
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
