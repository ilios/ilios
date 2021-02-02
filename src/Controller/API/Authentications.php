<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Authentication;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\AuthenticationRepository;
use App\Repository\UserRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\SessionUserProvider;
use App\Entity\AuthenticationInterface;
use App\Entity\UserInterface;
use App\Traits\ApiEntityValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

/**
 * Class AuthController
 * Authentication uses 'user' as the primary key and
 * needs to encode passwords
 * so we have to handle that specially.
 * @Route("/api/{version<v3>}/authentications")
 */
class Authentications
{
    use ApiEntityValidation;

    protected UserPasswordEncoderInterface $passwordEncoder;
    protected SessionUserProvider $sessionUserProvider;
    protected AuthenticationRepository $repository;
    protected UserRepository $userRepository;
    protected SerializerInterface $serializer;

    public function __construct(
        UserPasswordEncoderInterface $passwordEncoder,
        SessionUserProvider $sessionUserProvider,
        AuthenticationRepository $repository,
        UserRepository $userRepository,
        SerializerInterface $serializer
    ) {
        $this->sessionUserProvider = $sessionUserProvider;
        $this->passwordEncoder = $passwordEncoder;
        $this->repository = $repository;
        $this->userRepository = $userRepository;
        $this->serializer = $serializer;
    }

    /**
     * @Route("/{id}", methods={"GET"})
     */
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
     * @Route("", methods={"POST"})
     */
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

        $needingHashedPassword = array_filter($arr, function ($obj) {
            return (!empty($obj->password) && !empty($obj->user));
        });
        $userIdsForHashing = array_map(function ($obj) {
            return $obj->user;
        }, $needingHashedPassword);
        //prefetch all the users we need for hashing
        $users = [];
        /** @var UserInterface $user */
        foreach ($this->userRepository->findBy(['id' => $userIdsForHashing]) as $user) {
            $users[$user->getId()] = $user;
        }

        $encodedPasswords = [];
        foreach ($arr as $obj) {
            if (!empty($obj->password) && !empty($obj->user)) {
                $user = $users[$obj->user];
                if ($user) {
                    $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                    $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, $obj->password);
                    $encodedPasswords[$user->getId()] = $encodedPassword;
                }
            }
            //unset the password here in case it is NULL and didn't satisfy the above condition
            unset($obj->password);
        }
        $json = json_encode($arr);
        $entities = $this->serializer->deserialize($json, $class, 'json');

        $this->validateAndAuthorizeEntities($entities, AbstractVoter::CREATE, $validator, $authorizationChecker);

        $entitiesByUserId = [];
        /** @var AuthenticationInterface $authentication */
        foreach ($entities as $authentication) {
            $entitiesByUserId[$authentication->getUser()->getId()] = $authentication;
        }

        foreach ($encodedPasswords as $userId => $password) {
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

    /**
     * Handles GET request for multiple entities
     * @Route("", methods={"GET"})
     */
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

        $filteredResults = array_filter($dtos, function ($object) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $object);
        });

        //Re-index numerically index the array
        $values = array_values($filteredResults);

        return $builder->buildResponseForGetAllRequest('authentications', $values, Response::HTTP_OK, $request);
    }

    /**
     * Along with taking user input, this also encodes passwords so they
     * can be stored safely in the database
     * @Route("/{id}", methods={"PUT"})
     */
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
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }
        $authObject = $requestParser->extractPutDataFromRequest($request, 'authentications');
        if (!empty($authObject->password) && !empty($authObject->user)) {
            /** @var UserInterface $user */
            $user = $this->userRepository->findOneBy(['id' => $authObject->user]);
            if ($user) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, $authObject->password);
            }
        }
        unset($authObject->password);

        $json = json_encode($authObject);
        $this->serializer->deserialize(
            $json,
            get_class($entity),
            'json',
            ['object_to_populate' => $entity]
        );
        if (isset($encodedPassword)) {
            $entity->setPasswordHash($encodedPassword);
        }

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);
        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest('authentications', $entity, $code, $request);
    }

    /**
     * Along with taking user input, this also encodes passwords so they
     * can be stored safely in the database
     * @Route("/{id}", methods={"PATCH"})
     */
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

        /** @var Authentication $entity */
        $entity = $this->repository->findOneBy(['user' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf("authentications/%s was not found.", $id));
        }

        $authObject = $requestParser->extractPutDataFromRequest($request, 'authentications');
        if (!empty($authObject->password) && !empty($authObject->user)) {
            /** @var UserInterface $user */
            $user = $this->userRepository->findOneBy(['id' => $authObject->user]);
            if ($user) {
                $sessionUser = $this->sessionUserProvider->createSessionUserFromUser($user);
                $encodedPassword = $this->passwordEncoder->encodePassword($sessionUser, $authObject->password);
            }
        }
        unset($authObject->password);

        $json = json_encode($authObject);
        $this->serializer->deserialize(
            $json,
            get_class($entity),
            'json',
            ['object_to_populate' => $entity]
        );
        if (isset($encodedPassword)) {
            $entity->setPasswordHash($encodedPassword);
        }

        $this->validateAndAuthorizeEntity($entity, AbstractVoter::EDIT, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        $dtos = $this->fetchDtosForEntities([$entity]);
        return $builder->buildResponseForPatchRequest('authentications', $dtos[0], Response::HTTP_OK, $request);
    }

    /**
     * Deletes a record by userId
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(
        string $version,
        int $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->repository->findOneBy(['user' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::DELETE, $entity)) {
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
        $ids = array_map(function (Authentication $entity) {
            return $entity->getUser()->getId();
        }, $entities);

        return $this->repository->findDTOsBy(['user' => $ids]);
    }
}
