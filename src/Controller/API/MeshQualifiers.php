<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\DTO\MeshQualifierDTO;
use App\Repository\MeshQualifierRepository;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

#[OA\Tag(name:'MeSH qualifiers')]
#[Route('/api/{version<v3>}/meshqualifiers')]
class MeshQualifiers extends AbstractApiController
{
    public function __construct(MeshQualifierRepository $repository)
    {
        parent::__construct($repository, 'meshqualifiers');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/meshqualifiers/{id}',
        summary: 'Fetch a single MeSH qualifier.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single MeSH qualifier.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'meshTerms',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: MeshQualifierDTO::class)
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
        path: "/api/{version}/meshqualifiers",
        summary: "Fetch all MeSH qualifiers.",
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
                description: 'An array of MeSH qualifiers.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'meshQualifiers',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: MeshQualifierDTO::class)
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
}
