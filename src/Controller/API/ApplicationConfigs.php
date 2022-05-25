<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\DTO\ApplicationConfigDTO;
use App\Repository\ApplicationConfigRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Annotation\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Application configuration')]
#[Route('/api/{version<v3>}/applicationconfigs')]
class ApplicationConfigs extends AbstractApiController
{
    public function __construct(ApplicationConfigRepository $repository)
    {
        parent::__construct($repository, 'applicationconfigs');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/applicationconfigs/{id}',
        summary: 'Fetch a single application configuration item.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path')
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'A single application configuration item.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'applicationConfigs',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: ApplicationConfigDTO::class)
                    )
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '404', description: 'Not found.')]
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
    #[OA\Get(
        path: "/api/{version}/applicationconfigs",
        summary: "Fetch all application configuration items.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(
                name: 'offset',
                description: 'Offset',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'limit',
                description: 'Limit results',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'integer')
            ),
            new OA\Parameter(
                name: 'order_by',
                description: 'Order by fields. Must be an array, i.e. <code>&order_by[id]=ASC&order_by[x]=DESC</code>',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
                style: "deepObject"
            ),
            new OA\Parameter(
                name: 'filters',
                description: 'Filter by fields. Must be an array, i.e. <code>&filters[id]=3</code>',
                in: 'query',
                required: false,
                schema: new OA\Schema(
                    type: 'array',
                    items: new OA\Items(type: 'string'),
                ),
                style: "deepObject"
            )
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'An array of application configuration items.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'applicationConfigs',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: ApplicationConfigDTO::class)
                    )
                )
            ],
            type: 'object'
        )
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
    #[OA\Post(
        path: '/api/{version}/applicationconfigs',
        summary: "Create application configuration items.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(
                name: 'body',
                in: 'body',
                required: true,
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            'applicationConfigs',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: ApplicationConfigDTO::class)
                            )
                        )
                    ],
                    type: 'object',
                )
            )
        ]
    )]
    #[OA\Response(
        response: '201',
        description: 'An array of newly created application configuration items.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'applicationConfigs',
                    type: 'array',
                    items: new OA\Items(
                        ref: new Model(type: ApplicationConfigDTO::class)
                    )
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '400', description: 'Bad Request Data.')]
    #[OA\Response(response: '403', description: 'Access Denied.')]
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
    #[OA\Put(
        path: '/api/{version}/applicationconfigs/{id}',
        summary: 'Update an application configuration item.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
            new OA\Parameter(
                name: 'body',
                in: 'body',
                required: true,
                schema: new OA\Schema(
                    properties: [
                        new OA\Property(
                            'applicationConfig',
                            ref: new Model(type: ApplicationConfigDTO::class),
                            type: 'object'
                        )
                    ],
                    type: 'object',
                )
            )
        ]
    )]
    #[OA\Response(
        response: '200',
        description: 'The updated application configuration item.',
        content: new OA\JsonContent(
            properties: [
                new OA\Property(
                    'applicationConfig',
                    ref: new Model(type: ApplicationConfigDTO::class)
                )
            ],
            type: 'object'
        )
    )]
    #[OA\Response(response: '400', description: 'Bad Request Data.')]
    #[OA\Response(response: '403', description: 'Access Denied.')]
    #[OA\Response(response: '404', description: 'Not Found.')]
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
    #[OA\Delete(
        path: '/api/{version}/applicationconfigs/{id}',
        summary: 'Delete an application configuration item.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path')
        ]
    )]
    #[OA\Response(response: '204', description: 'Deleted.')]
    #[OA\Response(response: '403', description: 'Access Denied.')]
    #[OA\Response(response: '404', description: 'Not Found.')]
    #[OA\Response(
        response: '500',
        description: 'Deletion failed (usually caused by non-cascading relationships).'
    )]
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        return $this->handleDelete($version, $id, $authorizationChecker);
    }
}
