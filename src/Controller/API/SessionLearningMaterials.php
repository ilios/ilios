<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\SessionLearningMaterialDTO;
use App\Repository\SessionLearningMaterialRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Session learning materials')]
#[Route('/api/{version<v3>}/sessionlearningmaterials')]
class SessionLearningMaterials extends AbstractApiController
{
    public function __construct(SessionLearningMaterialRepository $repository)
    {
        parent::__construct($repository, 'sessionlearningmaterials');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/sessionlearningmaterials/{id}',
        summary: 'Fetch a single session learning material.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single session learning material.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'sessionLearningMaterials',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: SessionLearningMaterialDTO::class)
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
        Request $request,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $dto = $this->repository->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $values = $authorizationChecker->isGranted(VoterPermissions::VIEW, $dto) ? [$dto] : [];

        $currentUser = $tokenStorage->getToken()->getUser();
        if ($currentUser instanceof SessionUserInterface) {
            array_walk(
                $values,
                fn(SessionLearningMaterialDTO $dto) => $this->cleanDto($currentUser, $dto)
            );
        }

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    #[Route(
        methods: ['GET']
    )]
    #[OA\Get(
        path: "/api/{version}/sessionlearningmaterials",
        summary: "Fetch all session learning materials.",
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
                description: 'An array of session learning materials.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'sessionLearningMaterials',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: SessionLearningMaterialDTO::class)
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
        ApiResponseBuilder $builder,
        TokenStorageInterface $tokenStorage
    ): Response {
        $parameters = ApiRequestParser::extractParameters($request);

        $dtos = $this->repository->findDTOsBy(
            $parameters['criteria'],
            $parameters['orderBy'],
            $parameters['limit'],
            $parameters['offset']
        );

        $filteredResults = array_filter(
            $dtos,
            fn($object) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $object)
        );

        $currentUser = $tokenStorage->getToken()->getUser();
        if ($currentUser instanceof SessionUserInterface) {
            array_walk(
                $filteredResults,
                fn(SessionLearningMaterialDTO $dto) => $this->cleanDto($currentUser, $dto)
            );
        }

        //Re-index numerically index the array
        $values = array_values($filteredResults);

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    #[Route(methods: ['POST'])]
    #[OA\Post(
        path: '/api/{version}/sessionlearningmaterials',
        summary: "Create session learning materials.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'sessionLearningMaterials',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: SessionLearningMaterialDTO::class)
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
                description: 'An array of newly created session learning materials.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'sessionLearningMaterials',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: SessionLearningMaterialDTO::class)
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
        return $this->handlePost($version, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/sessionlearningmaterials/{id}',
        summary: 'Update or create a session learning material.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'sessionLearningMaterial',
                        ref: new Model(type: SessionLearningMaterialDTO::class),
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
                description: 'The updated session learning material.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'sessionLearningMaterial',
                            ref: new Model(type: SessionLearningMaterialDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created session learning material.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'sessionLearningMaterial',
                            ref: new Model(type: SessionLearningMaterialDTO::class)
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
        path: '/api/{version}/sessionlearningmaterials/{id}',
        summary: 'Delete a session learning material.',
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
        return $this->handleDelete($version, $id, $authorizationChecker);
    }

    /**
     * Remove notes from DTO for students who don't have permission to see it.
     */
    protected function cleanDto(
        SessionUserInterface $sessionUser,
        SessionLearningMaterialDTO $dto
    ): void {
        if (!$dto->publicNotes && !$sessionUser->performsNonLearnerFunction()) {
            $dto->notes =  null;
        }
    }
}
