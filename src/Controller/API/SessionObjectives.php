<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\SessionObjectiveManager;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class SessionObjectives extends ReadWriteController
{
    public function __construct(SessionObjectiveManager $manager)
    {
        parent::__construct($manager, 'sessionobjectives');
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        return parent::getOne($version, $id, $authorizationChecker, $builder, $request);
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives", methods={"GET"})
     */
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return parent::getAll($version, $request, $authorizationChecker, $builder);
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives/{id}", methods={"PUT"})
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
        return parent::put($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives", methods={"POST"})
     */
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return parent::post($version, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives/{id}", methods={"PATCH"})
     */
    public function patch(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        return parent::patch($version, $id, $request, $requestParser, $validator, $authorizationChecker, $builder);
    }

    /**
     * @Route("/api/{version<v3>}/sessionobjectives/{id}", methods={"DELETE"})
     */
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        return parent::delete($version, $id, $authorizationChecker);
    }
}
