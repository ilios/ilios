<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

/**
 * Class V1CompatibleApiController
 *
 * Default Controller for all API v1-compatible endpoints.
 */
class V1CompatibleApiController extends ApiController
{
    public function getAction($version, $object, $id)
    {
        $manager = $this->getManager($object);
        $dto = null;
        if ('v1' === $version) {
            $dto = $manager->findV1DTOBy(['id' => $id]);
        } else {
            $dto = $manager->findDTOBy(['id' => $id]);
        }

        if (! $dto) {
            $name = ucfirst($this->getSingularResponseKey($object));
            throw new NotFoundHttpException(sprintf("%s with id '%s' was not found.", $name, $id));
        }

        return $this->resultsToResponse([$dto], $this->getPluralResponseKey($object), Response::HTTP_OK);
    }

    public function getAllAction($version, $object, Request $request)
    {
        $parameters = $this->extractParameters($request);
        $manager = $this->getManager($object);
        if ('v1' === $version) {
            $result = $manager->findV1DTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        } else {
            $result = $manager->findDTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        }
        return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
    }
}
