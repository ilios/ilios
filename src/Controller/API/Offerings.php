<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\OfferingInterface;
use App\Entity\UserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\OfferingRepository;
use App\Repository\UserRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\ChangeAlertHandler;
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
        protected TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($repository, 'offerings');
    }

    #[Route(
        '/{id}',
        methods: ['GET']
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
        $class = $this->repository->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);

        $this->validateAndAuthorizeEntities($entities, AbstractVoter::CREATE, $validator, $authorizationChecker);

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

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     *
     * For offerings it also records an alert for the change
     *
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

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }
        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        /** @var OfferingInterface $entity */
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);

        $this->repository->update($entity, true, false);

        $session = $entity->getSession();
        if ($session && $session->isPublished()) {
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

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     *
     * For offerings it also records an alert for the change
     *
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

        /** @var OfferingInterface $entity */
        $entity = $this->repository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }
        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, AbstractVoter::EDIT, $validator, $authorizationChecker);

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
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        return $this->handleDelete($version, $id, $authorizationChecker);
    }

    protected function createAlertForNewOffering(OfferingInterface $offering)
    {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();

        /** @var UserInterface $instigator */
        $instigator = $this->userRepository->findOneBy(['id' => $sessionUser->getId()]);

        $this->alertHandler->createAlertForNewOffering($offering, $instigator);
    }

    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties
    ) {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();

        /** @var UserInterface $instigator */
        $instigator = $this->userRepository->findOneBy(['id' => $sessionUser->getId()]);
        $this->alertHandler->createOrUpdateAlertForUpdatedOffering($offering, $instigator, $originalProperties);
    }
}
