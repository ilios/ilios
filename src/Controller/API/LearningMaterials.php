<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterialInterface;
use App\RelationshipVoter\AbstractVoter;
use App\Repository\LearningMaterialRepository;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\IliosFileSystem;
use App\Service\TemporaryFileSystem;
use OpenApi\Attributes as OA;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

/**
 * learning materials are decorated with additional information
 * and also blanked for non-privleged users
 * otherwise the method bodies are copied from
 * the top level API Read and ReadWrite controllers
 *
 */
#[OA\Tag(name:'Learningmaterials')]
#[Route('/api/{version<v3>}/learningmaterials')]

class LearningMaterials
{
    protected string $endpoint;

    public function __construct(protected LearningMaterialRepository $repository)
    {
        $this->endpoint = 'learningmaterials';
    }

    /**
     * Handles GET request for a single entity
     */
    #[Route(
        '/{id}',
        methods: ['GET']
    )]
    public function getOne(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        TokenStorageInterface $tokenStorage
    ): Response {
        $dto = $this->repository->findDTOBy(['id' => $id]);

        if (! $dto) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $values =  [];
        if ($authorizationChecker->isGranted(AbstractVoter::VIEW, $dto)) {
            if (! $sessionUser->performsNonLearnerFunction()) {
                $dto->clearMaterial();
            }
            $values = [$dto];
        }

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles GET request for multiple entities
     */
    #[Route(methods: ['GET'])]
    public function getAll(
        string $version,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        TokenStorageInterface $tokenStorage
    ): Response {
        $parameters = ApiRequestParser::extractParameters($request);
        $q = $request->get('q');
        if (null !== $q) {
            $dtos = $this->repository->findDTOsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        } else {
            $dtos = $this->repository->findDTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        }

        $filteredResults = array_filter(
            $dtos,
            fn(LearningMaterialDTO $dto) => $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto)
        );

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $values = array_map(function (LearningMaterialDTO $dto) use ($sessionUser) {
            if (! $sessionUser->performsNonLearnerFunction()) {
                $dto->clearMaterial();
            }

            return $dto;
        }, $filteredResults);

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }


    /**
     * Handles POST which creates new data in the API
     * Connects file learning materials to the uploaded file
     * they are referencing and generate a token to use to link
     * to this learning material.
     */
    #[Route(methods: ['POST'])]
    public function post(
        string $version,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        TemporaryFileSystem $temporaryFileSystem,
        IliosFileSystem $fs,
        SerializerInterface $serializer
    ): Response {

        $data = $requestParser->extractPostDataFromRequest($request, 'learningmaterials');
        $dataWithFilesAttributes = array_map(function ($obj) use ($fs, $temporaryFileSystem) {
            $tmpFile = false;
            if (property_exists($obj, 'fileHash')) {
                $fileHash = $obj->fileHash;
                $contents = $fs->getUploadedTemporaryFileContentsAndRemoveFile($fileHash);
                $tmpFile = $contents ? $temporaryFileSystem->createFile($contents) : null;
                if (!$tmpFile || !$tmpFile->isReadable()) {
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        'This "fileHash" is not valid or the file may have already been used in a previous upload'
                    );
                }
                unset($obj->fileHash);
                $obj->mimetype = $tmpFile->getMimeType();
                $obj->relativePath = $fs->getLearningMaterialFilePath($tmpFile);
                $obj->filesize = $tmpFile->getSize();
            } else {
                unset($obj->mimetype);
                unset($obj->relativePath);
                unset($obj->filesize);
            }
            if ($tmpFile) {
                $fs->storeLearningMaterialFile($tmpFile);
                unlink($tmpFile->getRealPath());
            }

            return $obj;
        }, $data);


        $class = $this->repository->getClass();
        $entities = [];
        foreach ($dataWithFilesAttributes as $obj) {
            $relativePath = property_exists($obj, 'relativePath') ? $obj->relativePath : null;
            unset($obj->relativePath);
            $json = json_encode($obj);
            /** @var LearningMaterialInterface $entity */
            $entity = $serializer->deserialize($json, $class, 'json');
            if ($relativePath) {
                $entity->setRelativePath($relativePath);
            }
            $this->repository->update($entity, false);
            $this->validateLmEntity($entity, $validator);
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $entity)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $entities[] = $entity;
        }
        $this->repository->flush();

        $values = [];
        foreach ($entities as $entity) {
            $entity->generateToken();
            $this->repository->update($entity, false);
            $values[] = $entity;
        }
        $this->repository->flush();

        return $builder->buildResponseForPostRequest($this->endpoint, $values, Response::HTTP_CREATED, $request);
    }

    /**
     * When saving a learning material do not allow
     * the modification of file fields.  These are not
     * technically read only, but should not be writable when saved.
     */
    #[Route(
        '/{id}',
        methods: ['PUT']
    )]
    public function put(
        string $version,
        int $id,
        Request $request,
        ApiRequestParser $requestParser,
        ValidatorInterface $validator,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        SerializerInterface $serializer
    ): Response {
        /** @var LearningMaterialInterface $entity */
        $entity = $this->repository->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->repository->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }

        $data = $requestParser->extractPutDataFromRequest($request, 'learningmaterials');
        unset($data->fileHash);
        unset($data->mimetype);
        unset($data->relativePath);
        unset($data->filesize);

        $json = json_encode($data);
        $serializer->deserialize($json, $entity::class, 'json', ['object_to_populate' => $entity]);
        $this->validateLmEntity($entity, $validator);
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->repository->update($entity, true, false);

        return $builder->buildResponseForPutRequest(
            $this->endpoint,
            $entity,
            $code,
            $request
        );
    }

    /**
     * When saving a learning material do not allow
     * the modification of file fields.  These are not
     * technically read only, but should not be writable when saved.
     */
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
        ApiResponseBuilder $builder,
        SerializerInterface $serializer
    ): Response {
        $type = $request->getAcceptableContentTypes();
        if (!in_array("application/vnd.api+json", $type)) {
            throw new BadRequestHttpException("PATCH is only allowed for JSON:API requests, use PUT instead");
        }
        /** @var LearningMaterialInterface $entity */
        $entity = $this->repository->findOneBy(['id' => $id]);

        if (!$entity) {
            throw new NotFoundHttpException(sprintf("%s/%s was not found.", $this->endpoint, $id));
        }

        $data = $requestParser->extractPutDataFromRequest($request, 'learningmaterials');
        unset($data->fileHash);
        unset($data->mimetype);
        unset($data->relativePath);
        unset($data->filesize);

        $json = json_encode($data);
        $serializer->deserialize($json, $entity::class, 'json', ['object_to_populate' => $entity]);

        $this->validateLmEntity($entity, $validator);
        if (! $authorizationChecker->isGranted(AbstractVoter::EDIT, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->repository->update($entity, true, false);
        $dto = $this->repository->findDTOBy(['id' => $entity->getId()]);

        return $builder->buildResponseForPatchRequest(
            $this->endpoint,
            $dto,
            Response::HTTP_OK,
            $request
        );
    }

    /**
     * Handles DELETE requests to remove an element from the API
     */
    #[Route(
        '/{id}',
        methods: ['DELETE']
    )]
    public function delete(
        string $version,
        string $id,
        AuthorizationCheckerInterface $authorizationChecker
    ): Response {
        $entity = $this->repository->findOneBy(['id' => $id]);

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

    protected function validateLmEntity(LearningMaterialInterface $lm, ValidatorInterface $validator)
    {
        $errors = [];
        /** @var ConstraintViolationInterface $violation */
        foreach ($validator->validate($lm, null, $lm->getValidationGroups()) as $violation) {
            $property = $violation->getPropertyPath();
            $message = $violation->getMessage();
            $errors[] = "Error in *${property}*: ${message}";
        }
        if ($errors !== []) {
            $errorsString = implode("\n", $errors);
            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }
}
