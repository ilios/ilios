<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Entity\Manager\CohortManager;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\GoneHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * @Route("/api/{version<v1|v2>}/cohorts")
 */
class Cohorts extends ReadOnlyController
{
    public function __construct(CohortManager $manager)
    {
        parent::__construct($manager, 'cohorts');
    }

    /**
     * Don't allow new cohorts to be created with a PUT request,
     * otherwise edit them as usual.
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
    ) {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new GoneHttpException('Explicitly creating cohorts is not supported.');
        }
        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $errors = $validator->validate($entity);
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
        if (! $authorizationChecker->isGranted(AbstractVoter::EDIT, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->manager->update($entity, true, false);

        return $builder->buildSingularResponse($this->endpoint, $entity, Response::HTTP_OK);
    }

    /**
     * Handles DELETE requests to remove an element from the API
     * @Route("/{id}", methods={"DELETE"})
     */
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->manager->findOneBy(['id' => $id]);

        if (! $entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (! $authorizationChecker->isGranted(AbstractVoter::DELETE, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        try {
            $this->manager->delete($entity);

            return new Response('', Response::HTTP_NO_CONTENT);
        } catch (Exception $exception) {
            throw new RuntimeException("Failed to delete entity: " . $exception->getMessage());
        }
    }
}
