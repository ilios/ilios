<?php

declare(strict_types=1);

namespace App\Controller\API;

use App\Classes\SessionUserInterface;
use App\Entity\DTO\LearningMaterialDTO;
use App\Entity\LearningMaterialInterface;
use App\Entity\Manager\LearningMaterialManager;
use App\Entity\Manager\V1CompatibleBaseManager;
use App\RelationshipVoter\AbstractVoter;
use App\Service\ApiRequestParser;
use App\Service\ApiResponseBuilder;
use App\Service\IliosFileSystem;
use App\Service\LearningMaterialDecoratorFactory;
use App\Service\TemporaryFileSystem;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Exception;
use RuntimeException;

/**
 * learning materials are decorated with additional information
 * and also blanked for non-privleged users
 * otherwise the method bodies are copied from
 * the top level API Read and ReadWrite controllers
 *
 * @Route("/api/{version<v1|v2>}/learningmaterials")
 */

class LearningMaterials
{
    /**
     * @var LearningMaterialManager
     */
    protected $manager;

    /**
     * @var string
     */
    protected $endpoint;
    /**
     * @var LearningMaterialDecoratorFactory
     */
    protected $decoratorFactory;

    public function __construct(LearningMaterialManager $manager, LearningMaterialDecoratorFactory $decoratorFactory)
    {
        $this->manager = $manager;
        $this->endpoint = 'learningmaterials';
        $this->decoratorFactory = $decoratorFactory;
    }

    /**
     * Handles GET request for a single entity
     * @Route("/{id}", methods={"GET"})
     */
    public function getOne(
        string $version,
        int $id,
        Request $request,
        AuthorizationCheckerInterface $authorizationChecker,
        ApiResponseBuilder $builder,
        TokenStorageInterface $tokenStorage
    ): Response {
        if ('v1' === $version && ($this->manager instanceof V1CompatibleBaseManager)) {
            $dto = $this->manager->findV1DTOBy(['id' => $id]);
        } else {
            $dto = $this->manager->findDTOBy(['id' => $id]);
        }

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
            $values = [
                $this->decoratorFactory->create($dto)
            ];
        }

        return $builder->buildResponseForGetOneRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }

    /**
     * Handles GET request for multiple entities
     * @Route("", methods={"GET"})
     */
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
            $dtos = $this->manager->findLearningMaterialDTOsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        } elseif ('v1' === $version && ($this->manager instanceof V1CompatibleBaseManager)) {
            $dtos = $this->manager->findV1DTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        } else {
            $dtos = $this->manager->findDTOsBy(
                $parameters['criteria'],
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );
        }

        $filteredResults = array_filter($dtos, function (LearningMaterialDTO $dto) use ($authorizationChecker) {
            return $authorizationChecker->isGranted(AbstractVoter::VIEW, $dto);
        });

        /** @var SessionUserInterface $sessionUser */
        $sessionUser = $tokenStorage->getToken()->getUser();

        $values = [];
        foreach ($filteredResults as $object) {
            if (! $sessionUser->performsNonLearnerFunction()) {
                $object->clearMaterial();
            }
            $values[] = $this->decoratorFactory->create($object);
        }

        return $builder->buildResponseForGetAllRequest($this->endpoint, $values, Response::HTTP_OK, $request);
    }


    /**
     * Handles POST which creates new data in the API
     * Connects file learning materials to the uploaded file
     * they are referencing and generate a token to use to link
     * to this learning material.
     * @Route("", methods={"POST"})
     */
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
                $tmpFile = $temporaryFileSystem->createFile($contents);
                if (!$tmpFile || !$tmpFile->isReadable()) {
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        'This "fileHash" is not valid'
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


        $class = $this->manager->getClass();
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
            $this->manager->update($entity, false);

            $errors = $validator->validate($entity, null, $entity->getValidationGroups());
            if (count($errors) > 0) {
                $errorsString = (string) $errors;

                throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
            }
            if (! $authorizationChecker->isGranted(AbstractVoter::CREATE, $entity)) {
                throw new AccessDeniedException('Unauthorized access!');
            }

            $entities[] = $entity;
        }
        $this->manager->flush();

        $values = [];
        foreach ($entities as $entity) {
            $entity->generateToken();
            $this->manager->update($entity, false);
            $values[] = $this->decoratorFactory->create($entity);
        }
        $this->manager->flush();

        return $builder->buildResponseForPostRequest($this->endpoint, $values, Response::HTTP_CREATED, $request);
    }

    /**
     * When saving a learning material do not allow
     * the modification of file fields.  These are not
     * technically read only, but should not be writable when saved.
     * @Route("/{id}", methods={"PUT"})
     */
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
        $entity = $this->manager->findOneBy(['id' => $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = AbstractVoter::EDIT;
        } else {
            $entity = $this->manager->create();
            $code = Response::HTTP_CREATED;
            $permission = AbstractVoter::CREATE;
        }

        $data = $requestParser->extractPutDataFromRequest($request, 'learningmaterials');
        unset($data->fileHash);
        unset($data->mimetype);
        unset($data->relativePath);
        unset($data->filesize);

        $json = json_encode($data);
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);

        $errors = $validator->validate($entity, null, $entity->getValidationGroups());
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
        if (! $authorizationChecker->isGranted($permission, $entity)) {
            throw new AccessDeniedException('Unauthorized access!');
        }

        $this->manager->update($entity, true, false);

        return $builder->buildResponseForPutRequest(
            $this->endpoint,
            $this->decoratorFactory->create($entity),
            $code,
            $request
        );
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
