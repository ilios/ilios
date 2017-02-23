<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\CourseManager;
use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Ilios\CoreBundle\Entity\Manager\UserManager;
use Ilios\CoreBundle\Exception\InvalidInputWithSafeUserMessageException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class MeshDescriptorController
 * We have to handle a special 'q' parameter on meshDescriptors
 * so it needs its own controller
 * @package Ilios\ApiBundle\Controller
 */
class MeshDescriptorController extends ApiController
{
    public function getAllAction($version, $object, Request $request)
    {
        $q = $request->get('q');
        $parameters = $this->extractParameters($request);

        /** @var MeshDescriptorManager $manager */
        $manager = $this->getManager($object);

        if (null !== $q) {
            $result = $manager->findMeshDescriptorsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
        }

        return parent::getAllAction($version, $object, $request);
    }
}
