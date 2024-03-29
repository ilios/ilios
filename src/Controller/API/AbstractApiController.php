<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\VoterPermissions;
use App\Repository\RepositoryInterface;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Traits\ApiEntityValidation;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\PropertyAccess\PropertyAccess;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

abstract class AbstractApiController
{
    use ApiEntityValidation;

    public function __construct(protected RepositoryInterface $repository, protected string $endpoint)
    {
    }

    /**
     * Handles GET request for a single entity
     */
    protected function handleGetOne(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        Request $request
    ): Response {
        $dto = $this->repository->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $values = $authorizationChecker->isGranted(VoterPermissions::VIEW, $dto) ? [$dto] : [];

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles GET request for multiple entities
     */
    protected function handleGetAll(
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

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles POST which creates new data in the API
     */
    protected function handlePost(
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

        $dtos = $this->fetchDtosForEntities($entities);

        return $builder->buildResponseForPostRequest($this->endpoint, $dtos, Response::HTTP_CREATED, $request);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     */
    protected function handlePut(
        string $version,
        string $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder
    ): Response {
        $type = $request->getAcceptableContentTypes();
        if (in_array("application/vnd.api+json", $type)) {
            throw new BadRequestHttpException("PUT is not allowed for JSON:API requests, use PATCH instead");
        }
        $entity = $this->repository->findOneBy(['id' => $id]);
        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = VoterPermissions::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = VoterPermissions::CREATE;
        }

        $entity = $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);

        $this->validateAndAuthorizeEntity($entity, $permission, $validator, $authorizationChecker);

        $this->repository->update($entity);

        return $builder->buildResponseForPutRequest($this->endpoint, $entity, $code, $request);
    }

    /**
     * Modifies a single object in the API.  Can also create and
     * object if it does not yet exist.
     */
    protected function handlePatch(
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

        $requestParser->extractEntityFromPutRequest($request, $entity, $this->endpoint);
        $this->validateAndAuthorizeEntity($entity, VoterPermissions::EDIT, $validator, $authorizationChecker);
        $this->repository->update($entity);

        $dtos = $this->fetchDtosForEntities([$entity]);

        return $builder->buildResponseForPatchRequest($this->endpoint, $dtos[0], Response::HTTP_OK, $request);
    }

    /**
     * Handles DELETE requests to remove an element from the API
     */
    protected function handleDelete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->repository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        if (!$authorizationChecker->isGranted(VoterPermissions::DELETE, $entity)) {
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
        //read and deliver DTOs instead of Entities
        $idField = $this->repository->getIdField();
        $propertyAccessor = PropertyAccess::createPropertyAccessor();
        $ids = array_map(fn($entity) => $propertyAccessor->getValue($entity, $idField), $entities);

        return $this->repository->findDTOsBy(['id' => $ids]);
    }
}
