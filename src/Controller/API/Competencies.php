<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Repository\CompetencyRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Competencies extends ReadWriteController
{
    public function __construct(CompetencyRepository $repository)
    {
        parent::__construct($repository, 'competencies');
    }

    /**
     * @Route("/api/{version<v1|v3>}/competencies/{id}", methods={"GET"})
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
     * @Route("/api/{version<v1|v3>}/competencies", methods={"GET"})
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
     * @Route("/api/{version<v3>}/competencies/{id}", methods={"PUT"})
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
     * @Route("/api/{version<v3>}/competencies", methods={"POST"})
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
     * @Route("/api/{version<v3>}/competencies/{id}", methods={"PATCH"})
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
     * @Route("/api/{version<v3>}/competencies/{id}", methods={"DELETE"})
     */
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        return parent::delete($version, $id, $authorizationChecker);
    }
}
