<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\DTO\AamcPcrsDTO;
use App\Repository\AamcPcrsRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Traits\ApiAccessValidation;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'AAMC PCRSes')]
#[Route('/api/{version<v3>}/aamcpcrses')]
class AamcPcrses extends AbstractApiController
{
    use ApiAccessValidation;

    public function __construct(AamcPcrsRepository $repository, protected TokenStorageInterface $tokenStorage)
    {
        parent::__construct($repository, 'aamcpcrses');
    }
    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/aamcpcrses/{id}',
        summary: 'Fetch a single AAMC PCRS.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path', schema: new OA\Schema(type: 'integer')),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single AAMC PCRS.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'aamcPcrses',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AamcPcrsDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '404', description: 'Not found.'),
        ]
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
    #[OA\Get(
        path: "/api/{version}/aamcpcrses",
        summary: "Fetch all AAMC PCRSes.",
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
            ),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'An array of AAMC PCRSes.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'aamcPcrses',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AamcPcrsDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
        ]
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
        path: '/api/{version}/aamcpcrses',
        summary: "Create AAMC PCRSes.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'aamcPcrses',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: AamcPcrsDTO::class)
                        )
                    ),
                ],
                type: 'object',
            )
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '201',
                description: 'An array of newly created AAMC PCRSes.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'aamcPcrses',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AamcPcrsDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '400', description: 'Bad Request Data.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
        ]
    )]
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $this->validateCurrentUserAsSessionUser();
        return $this->handlePost($version, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/aamcpcrses/{id}',
        summary: 'Update or create an AAMC PCRS.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'aamcPcrs',
                        ref: new Model(type: AamcPcrsDTO::class),
                        type: 'object'
                    ),
                ],
                type: 'object',
            )
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'The updated AAMC PCRS.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'aamcPcrs',
                            ref: new Model(type: AamcPcrsDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created AAMC PCRS.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'aamcPcrs',
                            ref: new Model(type: AamcPcrsDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '400', description: 'Bad Request Data.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
        ]
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
        $this->validateCurrentUserAsSessionUser();
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
        $this->validateCurrentUserAsSessionUser();
        return $this->handlePatch($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/aamcpcrses/{id}',
        summary: 'Delete an AAMC PCRS.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(response: '204', description: 'Deleted.'),
            new OA\Response(response: '403', description: 'Access Denied.'),
            new OA\Response(response: '404', description: 'Not Found.'),
            new OA\Response(
                response: '500',
                description: 'Deletion failed (usually caused by non-cascading relationships).'
            ),
        ]
    )]
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $this->validateCurrentUserAsSessionUser();
        return $this->handleDelete($version, $id, $authorizationChecker);
    }
}
