<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\MeshDescriptorManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Class MeshDescriptorController
 * We have to handle a special 'q' parameter on meshDescriptors
 * so it needs its own controller
 */
class MeshDescriptorController extends ApiController
{
    /**
     * Handle 'q' parameter in queries
     * @inheritdoc
     */
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
