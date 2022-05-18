<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\RelationshipVoter\AbstractVoter;
use App\Repository\MeshDescriptorRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[Route('/api/{version<v3>}/meshdescriptors')]
class MeshDescriptors extends AbstractApiController
{
    public function __construct(protected MeshDescriptorRepository $meshDescriptorRepository)
    {
        parent::__construct($this->meshDescriptorRepository, 'meshdescriptors');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    public function getOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        return $this->handleGetOne($version, $id, $authorizationChecker, $builder, $request);
    }

    /**
     * Handle the special 'q' parameter
     */
    #[Route(methods: ['GET'])]
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $q = $request->get('q');
        $parameters = ApiRequestParser::extractParameters($request);

        if (null !== $q && '' !== $q) {
            $dtos = $this->meshDescriptorRepository->findDTOsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            $filteredResults = array_filter(
                $dtos,
                fn($object) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $object)
            );

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
        }

        return $this->handleGetAll($version, $request, $authorizationChecker, $builder);
    }
}
