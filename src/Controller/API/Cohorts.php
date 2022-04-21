<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\RelationshipVoter\AbstractVoter;
use App\Repository\CohortRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Traits\ApiEntityValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[Route('/api/{version<v3>}/cohorts')]
class Cohorts extends ReadOnlyController
{
    use ApiEntityValidation;

    public function __construct(CohortRepository $repository)
    {
        parent::__construct($repository, 'cohorts');
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
