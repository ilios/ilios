<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\UserDTO;
use App\Repository\UserRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Traits\ApiAccessValidation;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * Class UsersController
 * We have to handle a special 'q' parameter
 * as well as special handling for ICS feed keys
 * so users needs its own controller
 *
 */
#[OA\Tag(name:'Users')]
#[Route('/api/{version<v3>}/users')]
class Users extends AbstractApiController
{
    use ApiAccessValidation;

    public function __construct(
        protected UserRepository $userRepository,
        protected TokenStorageInterface $tokenStorage,
        protected SerializerInterface $serializer
    ) {
        parent::__construct($userRepository, 'users');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/users/{id}',
        summary: 'Fetch a single user.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single user.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'users',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: UserDTO::class)
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

    /**
     * Handle the special 'q' parameter for courses
     */
    #[Route(methods: ['GET'])]
    #[OA\Get(
        path: "/api/{version}/users",
        summary: "Fetch all users.",
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(
                name: 'q',
                description: 'Search filter',
                in: 'query',
                required: false,
                schema: new OA\Schema(type: 'string')
            ),
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
                description: 'An array of users.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'users',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: UserDTO::class)
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
        $q = $request->get('q');
        $parameters = ApiRequestParser::extractParameters($request);

        if (null !== $q && '' !== $q) {
            $dtos = $this->userRepository->findDTOsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset'],
                $parameters['criteria'],
            );

            $filteredResults = array_filter(
                $dtos,
                fn($object) => $authorizationChecker->isGranted(VoterPermissions::VIEW, $object)
            );

            //Re-index numerically index the array
            $values = array_values($filteredResults);

            return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
        }

        return $this->handleGetAll($version, $request, $authorizationChecker, $builder);
    }

    /**
     * When Users are submitted with an empty icsFeedKey value that overrides
     * the created key.  This happens when new users are created and they don't have a
     * key yet. Instead of using the blank key we need to keep the one that is generated
     * in the User entity constructor.
     */
    #[Route(methods: ['POST'])]
    #[OA\Post(
        path: '/api/{version}/users',
        summary: "Create users.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'users',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: UserDTO::class)
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
                description: 'An array of newly created users.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'users',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: UserDTO::class)
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
        $data = $requestParser->extractPostDataFromRequest($request, $this->endpoint);
        $dataWithoutEmptyIcsFeed = array_map(function ($obj) {
            if (is_object($obj) && property_exists($obj, 'icsFeedKey')) {
                if (empty($obj->icsFeedKey)) {
                    unset($obj->icsFeedKey);
                }
            }

            return $obj;
        }, $data);

        $class = $this->repository->getClass() . '[]';
        $json = json_encode($dataWithoutEmptyIcsFeed);
        $entities = $this->serializer->deserialize($json, $class, 'json');

        $this->validateAndAuthorizeEntities($entities, VoterPermissions::CREATE, $validator, $authorizationChecker);

        foreach ($entities as $entity) {
            $this->repository->update($entity, false);
        }
        $this->repository->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
    }

    /**
     * Only a root user can make other users root.
     * This has to be done here because by the time it reaches the voter the
     * current user object in the session has been modified
     */
    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/users/{id}',
        summary: 'Update or create a user.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'user',
                        ref: new Model(type: UserDTO::class),
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
                description: 'The updated user.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'user',
                            ref: new Model(type: UserDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created user.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'user',
                            ref: new Model(type: UserDTO::class)
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
        /** @var SessionUserInterface $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $entity = $this->repository->findOneBy(['id' => $id]);
        if ($entity) {
            $obj = $requestParser->extractPutDataFromRequest($request, $this->endpoint);
            if (
                //adding root by a non-root user
                ($obj->root &&
                (!$currentUser->isRoot() && !$entity->isRoot())) ||

                //removing root be a non-root user
                (!$obj->root &&
                (!$currentUser->isRoot() && $entity->isRoot()))
            ) {
                throw new AccessDeniedException('Unauthorized access!');
            }
        }

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
        $type = $request->getAcceptableContentTypes();
        if (!in_array("application/vnd.api+json", $type)) {
            throw new BadRequestHttpException("PATCH is only allowed for JSON:API requests, use PUT instead");
        }
        $this->validateCurrentUserAsSessionUser();
        /** @var SessionUserInterface $currentUser */
        $currentUser = $this->tokenStorage->getToken()->getUser();
        $entity = $this->repository->findOneBy(['id' => $id]);
        if ($entity) {
            $arr = $requestParser->extractJsonApiPatchDataFromRequest($request);
            if (
                //adding root by a non-root user
                ($arr['root'] &&
                    (!$currentUser->isRoot() && !$entity->isRoot())) ||

                //removing root be a non-root user
                (!$arr['root'] &&
                    (!$currentUser->isRoot() && $entity->isRoot()))
            ) {
                throw new AccessDeniedException('Unauthorized access!');
            }
        }
        return $this->handlePatch($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/users/{id}',
        summary: 'Delete a user.',
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
