<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\RelationshipVoter\AbstractVoter;
use App\Repository\CohortRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Traits\ApiEntityValidation;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Cohorts')]
#[Route('/api/{version<v3>}/cohorts')]
class Cohorts extends AbstractApiController
{
    use ApiEntityValidation;

    public function __construct(CohortRepository $repository)
    {
        parent::__construct($repository, 'cohorts');
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

    #[Route(
        methods: ['GET']
    )]
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return $this->handleGetAll($version, $request, $authorizationChecker, $builder);
    }

    /**
     * Don't allow new cohorts to be created with a PUT request,
     * otherwise edit them as usual.
     */
    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    public function put(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $entity = $this->repository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new GoneHttpException('Explicitly creating cohorts is not supported.');
        }
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, AbstractVoter::EDIT, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, Response::HTTP_OK, $request);
    }
}
