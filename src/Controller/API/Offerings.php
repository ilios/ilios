<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\Manager\OfferingManager;
use App\Entity\Manager\UserManager;
use App\Entity\OfferingInterface;
use App\Entity\UserInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\ChangeAlertHandler;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/{version<v1|v2>}/offerings")
 */
class Offerings extends ReadWriteController
{
    /**
     * @var ChangeAlertHandler
     */
    protected $alertHandler;
    /**
     * @var UserManager
     */
    protected $userManager;
    /**
     * @var TokenStorageInterface
     */
    protected $tokenStorage;

    public function __construct(
        OfferingManager $manager,
        ChangeAlertHandler $alertHandler,
        UserManager $userManager,
        TokenStorageInterface $tokenStorage
    ) {
        parent::__construct($manager, 'offerings');
        $this->alertHandler = $alertHandler;
        $this->userManager = $userManager;
        $this->tokenStorage = $tokenStorage;
    }


    /**
     * Handles POST which creates new data in the API
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
        $class = $this->manager->getClass() . '[]';

        $entities = $requestParser->extractEntitiesFromPostRequest($request, $class, $this->endpoint);

        foreach ($entities as $entity) {
            $errors = $validator->validate($entity);
            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $entity)) {
                throw new AccessDeniedException('Unauthorized access!');
            }
        }

        foreach ($entities as $entity) {
            $this->manager->update($entity, false);
        }

        $this->manager->flush();

        foreach ($entities as $entity) {
            $session = $entity->getSession();
            if ($session && $session->isPublished()) {
                $this->createAlertForNewOffering($entity);
            }
        }

        $this->manager->flush();

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     * @Route("/{id}", methods={"PUT"})
     */
    public function put(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->manager->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }
        // capture the values of offering properties pre-update
        $alertProperties = $entity->getAlertProperties();

        /** @var OfferingInterface $entity */
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->manager->update($entity, true, false);

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
        }

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, $code, $request);
    }

    protected function createAlertForNewOffering(OfferingInterface $offering)
    {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();

        /** @var UserInterface $instigator */
        $instigator = $this->userManager->findOneBy(['id' => $sessionUser->getId()]);

        $this->alertHandler->createAlertForNewOffering($offering, $instigator);
    }

    protected function createOrUpdateAlertForUpdatedOffering(
        OfferingInterface $offering,
        array $originalProperties
    ) {
        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $this->tokenStorage->getToken()->getUser();

        /** @var UserInterface $instigator */
        $instigator = $this->userManager->findOneBy(['id' => $sessionUser->getId()]);
        $this->alertHandler->createOrUpdateAlertForUpdatedOffering($offering, $instigator, $originalProperties);
    }
}
