<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ManagerInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Service\EndpointResponseNamer;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Serializer\SerializerInterface;

abstract class BaseController
{
    /**
     * @var ManagerInterface
     */
    protected $manager;

    public function __construct(ManagerInterface $manager)
    {
        $this->manager = $manager;
    }

    /**
     * Handles GET request for a single entity
     */
    public function getOne(
        string $version,
        string $object,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        EndpointResponseNamer $endpointResponseNamer,
        SerializerInterface $serializer
    ): Response {
        $dto = $this->manager->findDTOBy(['id' => $id]);

        if (! $dto) {
            $name = $endpointResponseNamer->getSingularName($object);
            throw new NotFoundHttpException(sprintf("%s with id '%s' was not found.", $name, $id));
        }

        $values = $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto) ? [$dto] : [];

        return new Response(
            $serializer->serialize(
                [ $endpointResponseNamer->getPluralName($object) => $values],
                'json'
            ),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }

    /**
     * Handles GET request for multiple entities
     */
    public function getAll(
        string $version,
        string $object,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        EndpointResponseNamer $endpointResponseNamer,
        SerializerInterface $serializer
    ): Response {
        $parameters = $this->extractParameters($request);
        $dtos = $this->manager->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
        });

        //Re-index numerically index the array
        $values = array_values($filteredResults);

        return new Response(
            $serializer->serialize(
                [ $endpointResponseNamer->getPluralName($object) => $values],
                'json'
            ),
            Response::HTTP_OK,
            ['Content-type' => 'application/json']
        );
    }



    /**
     * Extract the non-data parameters which control the response we send
     */
    protected function extractParameters(Request $request): array
    {
        $parameters = [
            'offset' => $request->query->get('offset'),
            'limit' => $request->query->get('limit'),
            'orderBy' => $request->query->get('order_by'),
            'criteria' => []
        ];

        $criteria = !is_null($request->query->get('filters')) ? $request->query->get('filters') : [];
        $criteria = array_map(function ($item) {
            //convert boolean/null strings to boolean/null values
            $item = $item === 'null' ? null : $item;
            $item = $item === 'false' ? false : $item;
            $item = $item === 'true' ? true : $item;

            return $item;
        }, $criteria);

        $parameters['criteria'] = $criteria;

        return $parameters;
    }
}
