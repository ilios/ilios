<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\ServiceTokenUserInterface;
use App\Classes\SessionUserInterface;
use App\Classes\VoterPermissions;
use App\Entity\DTO\OfferingDTO;
use App\Entity\OfferingInterface;
use App\Entity\UserInterface;
use App\Repository\OfferingRepository;
use App\Repository\ServiceTokenRepository;
use App\Repository\UserRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\ChangeAlertHandler;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Offerings')]
#[Route('/api/{version<v3>}/offerings')]
class Offerings extends AbstractApiController
{
    public function __construct(
        OfferingRepository $repository,
        protected ChangeAlertHandler $alertHandler,
        protected UserRepository $userRepository,
        protected ServiceTokenRepository $serviceTokenRepository,
        protected TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($repository, 'offerings');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/offerings/{id}',
        summary: 'Fetch a single offering.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single offering.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'offerings',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: OfferingDTO::class)
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
        path: "/api/{version}/offerings",
        summary: "Fetch all offerings.",
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
                description: 'An array of offerings.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'offerings',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: OfferingDTO::class)
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
        path: '/api/{version}/offerings',
        summary: "Create offerings.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'offerings',
                        type: 'array',
                        items: new OA\Items(
                            ref: new Model(type: OfferingDTO::class)
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
                description: 'An array of newly offerings.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'offerings',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: OfferingDTO::class)
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
        $class = $this->repository->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);

        $this->validateAndAuthorizeEntities($entities, VoterPermissions::CREATE, $validator, $authorizationChecker);

        foreach ($entities as $entity) {
            $this->repository->update($entity, false);
        }

        $this->repository->flush();

        foreach ($entities as $entity) {
            $session = $entity->getSession();
            if ($session && $session->isPublished()) {
                $this->createAlertForNewOffering($entity);
            }
        }

        $this->repository->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
    }

    /*
     * For offerings it also records an alert for the change
     */
    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/offerings/{id}',
        summary: 'Update or create an offering.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'offering',
                        ref: new Model(type: OfferingDTO::class),
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
                description: 'The updated offering.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'offering',
                            ref: new Model(type: OfferingDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created offering.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'offering',
                            ref: new Model(type: OfferingDTO::class)
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
        $entity = $this->repository->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = VoterPermissions::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = VoterPermissions::CREATE;
        }
        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        /** @var OfferingInterface $entity */
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        $session = $entity->getSession();
        if ($session->isPublished()) {
            if (Response::HTTP_CREATED === $code) {
                $this->createAlertForNewOffering($entity);
            } else {
                $this->createOrUpdateAlertForUpdatedOffering(
                    $entity,
                    $alertProperties
                );
            }
            $this->repository->flush();
        }

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, $code, $request);
    }

    /*
     * For offerings it also records an alert for the change
     */
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

        $entity = $this->repository->findOneBy(['id' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }
        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, VoterPermissions::EDIT, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        $session = $entity->getSession();
        if ($session && $session->isPublished()) {
            $this->createOrUpdateAlertForUpdatedOffering(
                $entity,
                $alertProperties
            );
            $this->repository->flush();
        }

        $dtos = $this->fetchDtosForEntities([$entity]);
        return $builder->buildResponseForPatchRequest($this->endpoint, $dtos[0], Response::HTTP_OK, $request);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/offerings/{id}',
        summary: 'Delete an offering.',
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

    protected function createAlertForNewOffering(OfferingInterface $offering): void
    {
        $user = null;
        $serviceToken = null;

        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if ($sessionUser instanceof SessionUserInterface) {
            $user = $this->userRepository->findOneBy(['id' => $sessionUser->getId()]);
        } elseif ($sessionUser instanceof ServiceTokenUserInterface) {
            $serviceToken = $this->serviceTokenRepository->findOneBy(['id' => $sessionUser->getId()]);
        }

        $this->alertHandler->createAlertForNewOffering($offering, $user, $serviceToken);
    }

    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties
    ): void {
        $user = null;
        $serviceToken = null;

        $sessionUser = $this->tokenStorage->getToken()->getUser();
        if ($sessionUser instanceof SessionUserInterface) {
            $user = $this->userRepository->findOneBy(['id' => $sessionUser->getId()]);
        } elseif ($sessionUser instanceof ServiceTokenUserInterface) {
            $serviceToken = $this->serviceTokenRepository->findOneBy(['id' => $sessionUser->getId()]);
        }

        $this->alertHandler->createOrUpdateAlertForUpdatedOffering(
            $offering,
            $originalProperties,
            $user,
            $serviceToken
        );
    }
}
