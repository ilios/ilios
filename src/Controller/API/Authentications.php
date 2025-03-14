<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\VoterPermissions;
use App\Entity\Authentication;
use App\Entity\AuthenticationInterface;
use App\Entity\DTO\AuthenticationDTO;
use App\Entity\UserInterface;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\SessionUserProvider;
use App\Traits\ApiEntityValidation;
use Exception;
use Nelmio\ApiDocBundle\Attribute\Model;
use OpenApi\Attributes as OA;
use RuntimeException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

#[OA\Tag(name:'Authentications')]
#[Route('/api/{version<v3>}/authentications')]
class Authentications
{
    use ApiEntityValidation;

    public function __construct(
        protected UserPasswordHasherInterface $passwordHasher,
        protected SessionUserProvider $sessionUserProvider,
        protected AuthenticationRepository $repository,
        protected UserRepository $userRepository,
        protected SerializerInterface $serializer
    ) {
    }


    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    #[OA\Get(
        path: '/api/{version}/authentications/{id}',
        summary: 'Fetch a single authentication record.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'user id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'A single authentication record.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'authentication',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AuthenticationDTO::class)
                            )
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(response: '404', description: 'Not found.'),
        ]
    )]
    public function getOne(string $version, int $id, ApiResponseBuilder $builder, Request $request): Response
    {
        $dto = $this->repository->findDTOBy(['user' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $builder->buildResponseForGetOneRequest('authentications', [$dto], Response::HTTP_OK, $request);
    }

    /**
     * Along with taking input this also encodes the passwords
     * so they can be stored safely in the database
     */
    #[Route(methods: ['POST'])]
    #[OA\Post(
        path: '/api/{version}/authentications',
        summary: "Create authentication records.",
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'authentications',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property("user", type: "integer"),
                                new OA\Property("username", type: "string"),
                                new OA\Property("password", type: "string"),
                            ],
                            type: "object"
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
                description: 'An array of newly created authentication records.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'authentications',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AuthenticationDTO::class)
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
        $arr = $requestParser->extractPostDataFromRequest($request, 'authentications');

        $needingHashedPassword = array_filter($arr, fn($obj) => !empty($obj->password) && !empty($obj->user));
        $userIdsForHashing = array_map(fn($obj) => $obj->user, $needingHashedPassword);
        //prefetch all the users we need for hashing
        $users = [];
        foreach ($this->userRepository->findBy(['id' => $userIdsForHashing]) as $user) {
            $users[$user->getId()] = $user;
        }

        $hashedPasswords = [];
        foreach ($arr as $obj) {
            if (!empty($obj->password) && !empty($obj->user) && array_key_exists($obj->user, $users)) {
                $user = $users[$obj->user];
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                $hashedPassword = $this->passwordHasher->hashPassword($sessionUser, $obj->password);
                $hashedPasswords[$user->getId()] = $hashedPassword;
            }
            //unset the password here in case it is NULL and didn't satisfy the above condition
            unset($obj->password);
        }
        $json = json_encode($arr);
        $entities = $this->serializer->deserialize($json, $class, 'json');

        $this->validateAndAuthorizeEntities($entities, VoterPermissions::CREATE, $validator, $authorizationChecker);

        $entitiesByUserId = [];
        /** @var AuthenticationInterface $authentication */
        foreach ($entities as $authentication) {
            $entitiesByUserId[$authentication->getUser()->getId()] = $authentication;
        }

        foreach ($hashedPasswords as $userId => $password) {
            $entitiesByUserId[$userId]->setPasswordHash($password);
        }
        $entities = array_values($entitiesByUserId);

        foreach ($entities as $entity) {
            $this->repository->update($entity, false);
        }
        $this->repository->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest('authentications', $dtos, Response::HTTP_CREATED, $request);
    }

    #[Route(methods: ['GET'])]
    #[OA\Get(
        path: "/api/{version}/authentications",
        summary: "Fetch all authentication records.",
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
                description: 'An array of authentication records.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'authentications',
                            type: 'array',
                            items: new OA\Items(
                                ref: new Model(type: AuthenticationDTO::class)
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

        //Re-index numerically index the array
        $values = array_values($filteredResults);

        return $builder->buildResponseForGetAllRequest('authentications', $values, Response::HTTP_OK, $request);
    }

    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    #[OA\Put(
        path: '/api/{version}/authentications/{id}',
        summary: 'Update or create an authentication record.',
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                properties: [
                    new OA\Property(
                        'authentication',
                        type: 'array',
                        items: new OA\Items(
                            properties: [
                                new OA\Property("user", type: "integer"),
                                new OA\Property("username", type: "string"),
                                new OA\Property("password", type: "string"),
                            ],
                            type: "object"
                        )
                    ),
                ],
                type: 'object',
            )
        ),
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'user id', in: 'path'),
        ],
        responses: [
            new OA\Response(
                response: '200',
                description: 'The updated authentication record.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'authentication',
                            ref: new Model(type: AuthenticationDTO::class)
                        ),
                    ],
                    type: 'object'
                )
            ),
            new OA\Response(
                response: '201',
                description: 'The newly created authentication record.',
                content: new OA\JsonContent(
                    properties: [
                        new OA\Property(
                            'authentication',
                            ref: new Model(type: AuthenticationDTO::class)
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
        int $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $entity = $this->repository->findOneBy(['user' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = VoterPermissions::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = VoterPermissions::CREATE;
        }
        $authObject = $requestParser->extractPutDataFromRequest($request, 'authentications');
        if (!empty($authObject->password) && !empty($authObject->user)) {
            $user = $this->userRepository->findOneBy(['id' => $authObject->user]);
            if ($user) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                $hashedPassword = $this->passwordHasher->hashPassword($sessionUser, $authObject->password);
            }
        }
        unset($authObject->password);

        $json = json_encode($authObject);
        $this->serializer->deserialize(
            $json,
            $entity::class,
            'json',
            ['object_to_populate' => $entity]
        );
        if (isset($hashedPassword)) {
            $entity->setPasswordHash($hashedPassword);
        }

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest('authentications', $entity, $code, $request);
    }

    #[Route(
        '/{id}',
        methods: ['PATCH']
    )]
    public function patch(
        string $version,
        int $id,
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

        $entity = $this->repository->findOneBy(['user' => $id]);
        if (!$entity) {
            throw new NotFoundHttpException(sprintf("authentications/%s was not found.", $id));
        }

        $authObject = $requestParser->extractPutDataFromRequest($request, 'authentications');
        if (!empty($authObject->password) && !empty($authObject->user)) {
            $user = $this->userRepository->findOneBy(['id' => $authObject->user]);
            if ($user) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                $hashedPassword = $this->passwordHasher->hashPassword($sessionUser, $authObject->password);
            }
        }
        unset($authObject->password);

        $json = json_encode($authObject);
        $this->serializer->deserialize(
            $json,
            $entity::class,
            'json',
            ['object_to_populate' => $entity]
        );
        if (isset($hashedPassword)) {
            $entity->setPasswordHash($hashedPassword);
        }

        $this->validateAndAuthorizeEntity($entity, VoterPermissions::EDIT, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        $dtos = $this->fetchDtosForEntities([$entity]);
        return $builder->buildResponseForPatchRequest('authentications', $dtos[0], Response::HTTP_OK, $request);
    }

    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    #[OA\Delete(
        path: '/api/{version}/authentications/{id}',
        summary: 'Delete an authentication record.',
        parameters: [
            new OA\Parameter(name: 'version', description: 'API Version', in: 'path'),
            new OA\Parameter(name: 'id', description: 'user id', in: 'path'),
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
        int $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->repository->findOneBy(['user' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(VoterPermissions::DELETE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        try {
            $this->repository->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to delete entity: " . $exception->getMessage());
        }
    }

    protected function fetchDtosForEntities(array $entities): array
    {
        $ids = array_map(fn(Authentication $entity) => $entity->getUser()->getId(), $entities);

        return $this->repository->findDTOsBy(['user' => $ids]);
    }
}
