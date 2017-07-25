<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\Manager\BaseManager;
use Ilios\CoreBundle\Entity\Manager\DTOManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class NonDtoApiController
 *
 * Some entities have not yet been converted to use plain
 * PHP objects and a special repository method when fetched
 *
 * These have to be found differently
 *
 */
class NonDtoApiController extends ApiController
{
    /**
     * @inheritdoc
     */
    public function getAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $this->resultsToResponse([$entity], $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    /**
     * @inheritdoc
     */
    public function getAllAction($version, $object, Request $request)
    {
        $parameters = $this->extractParameters($request);
        $manager = $this->getManager($object);
        $result = $manager->findBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    /**
     * Get a manager which does not handle DTOs and confirm
     * that the endpoint should not be using one that does
     *
     * @param string $pluralObjectName
     * @return BaseManager
     * @throws \Exception
     */
    protected function getManager($pluralObjectName)
    {
        $entityName = $this->getEntityName($pluralObjectName);
        $name = "Ilios\\CoreBundle\\Entity\\Manager\\${entityName}Manager";
        if (!$this->container->has($name)) {
            throw new \Exception(
                sprintf('The manager for \'%s\' does not exist.', $pluralObjectName)
            );
        }

        /** @var BaseManager $manager */
        $manager = $this->container->get($name);

        if ($manager instanceof DTOManagerInterface) {
            $class = $manager->getClass();
            throw new \Exception(
                "{$class} is DTO enabled and should be removed " .
                'from the "ilios_api_non_dto_controllers" parameter.'
            );
        }

        return $manager;
    }
}
