<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\RelationshipVoter\AbstractVoter;
use App\Repository\SessionRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

/**
 * @Route("/api/{version<v3>}/sessions")
 */
class Sessions extends ReadWriteController
{
    public function __construct(protected SessionRepository $sessionRepository)
    {
        parent::__construct($sessionRepository, 'sessions');
    }

    /**
     * Handle the special 'q' parameter for sessions
     * @Route("", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $q = $request->get('q');
        $parameters = ApiRequestParser::extractParameters($request);

        if (null !== $q && '' !== $q) {
            $dtos = $this->sessionRepository->findDTOsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset'],
                $parameters['criteria'],
            );

            $filteredResults = array_filter(
                $dtos,
                fn($object) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $object)
            );

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
        }

        return parent::getAll($version, $request, $authorizationChecker, $builder);
    }
}
