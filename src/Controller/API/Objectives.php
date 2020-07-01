<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\ObjectiveManager;
use App\Entity\Manager\V1CompatibleBaseManager;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/api/{version<v1>}/objectives")
 */
class Objectives extends ReadOnlyController
{
    public function __construct(ObjectiveManager $manager)
    {
        parent::__construct($manager, 'objectives');
    }

    /**
     * Handles GET request for a single entity
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        $dto = $this->manager->findV1DTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $values = $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto) ? [$dto] : [];

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles GET request for multiple entities
     * @Route("", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $parameters = ApiRequestParser::extractParameters($request);

        $dtos = $this->manager->findV1DTOsBy(
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

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }
}
