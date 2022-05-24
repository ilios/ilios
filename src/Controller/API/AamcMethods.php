<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\DTO\AamcMethodDTO;
use App\Repository\AamcMethodRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Aamcmethods')]
#[Route('/api/{version<v3>}/aamcmethods', defaults: ['version' => 'v3'])]
class AamcMethods extends AbstractApiController
{
    public function __construct(AamcMethodRepository $repository)
    {
        parent::__construct($repository, 'aamcmethods');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: "/api/{version}/aamcmethods/{id}",
        summary: "Get one AAMC method by its id",
        parameters: [
            new OA\Parameter(name: 'offset', description: 'Offering', in: 'query'),
            new OA\Parameter(name: 'limit', description: 'Limit Results', in: 'query')
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'A single Aamcmethod',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    "aamcmethods",
                    type: "array",
                    items: new OA\Items(
                        ref: new Model(type: AamcMethodDTO::class)
                    )
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '404', description: 'Not found')]
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

    #[Route(methods: ['POST'])]
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return $this->handlePost($version, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

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
        return $this->handlePut($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['PATCH']
    )]
    public function patch(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return $this->handlePatch($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        return $this->handleDelete($version, $id, $authorizationChecker);
    }
}
