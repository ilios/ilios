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
}
