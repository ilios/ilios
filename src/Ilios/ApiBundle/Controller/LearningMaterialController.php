<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Service\IliosFileSystem;
use Ilios\CoreBundle\Service\TemporaryFileSystem;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Ilios\CoreBundle\Service\LearningMaterialDecoratorFactory;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class LearningMaterialController
 * We have to handle a special 'q' parameter on learningMaterials
 * and we need to work with a factory to produce the results
 * so it needs its own controller
 */
class LearningMaterialController extends ApiController
{
    /**
     * @var TemporaryFileSystem
     */
    protected $temporaryFileSystem;

    /**
     * @var IliosFileSystem
     */
    protected $fileSystem;

    /**
     * @var LearningMaterialDecoratorFactory
     */
    protected $learningMaterialDecoratorFactory;

    /**
     * Inject services
     *
     * Since we're inheriting our actions from the ApiController it is easier to just
     * inject these needed services here
     *
     * @required
     *
     * @param IliosFileSystem $filesystem
     * @param TemporaryFileSystem $temporaryFileSystem
     * @param LearningMaterialDecoratorFactory $learningMaterialDecoratorFactory
     */
    public function setup(
        IliosFileSystem $filesystem,
        TemporaryFileSystem $temporaryFileSystem,
        LearningMaterialDecoratorFactory $learningMaterialDecoratorFactory
    ) {
        $this->fileSystem = $filesystem;
        $this->temporaryFileSystem = $temporaryFileSystem;
        $this->learningMaterialDecoratorFactory = $learningMaterialDecoratorFactory;
    }

    /**
     * Handle 'q' as a special parameter to search with
     * @inheritdoc
     */
    public function getAllAction($version, $object, Request $request)
    {
        $q = $request->get('q');
        if (null !== $q) {
            /** @var LearningMaterialManager $manager */
            $manager = $this->getManager($object);
            $parameters = $this->extractParameters($request);
            $result = $manager->findLearningMaterialsByQ(
                $q,
                $parameters['orderBy'],
                $parameters['limit'],
                $parameters['offset']
            );

            return $this->resultsToResponse($result, $this->getPluralResponseKey($object), Response::HTTP_OK);
        }

        return parent::getAllAction($version, $object, $request);
    }

    /**
     * Connects file learning materials to the uploaded file
     * they are referencing and generate a token to use to link
     * to this learning material.
     *
     * @inheritdoc
     */
    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);

        $data = $this->extractPostDataFromRequest($request, $object);
        $temporaryFileSystem = $this->temporaryFileSystem;
        $fs = $this->fileSystem;
        $dataWithFilesAttributes = array_map(function ($obj) use ($fs, $temporaryFileSystem) {
            $file = false;
            if (property_exists($obj, 'fileHash')) {
                $fileHash = $obj->fileHash;
                $file = $temporaryFileSystem->getFile($fileHash);

                if (!$file->isReadable()) {
                    throw new HttpException(
                        Response::HTTP_BAD_REQUEST,
                        'This "fileHash" is not valid'
                    );
                }
                unset($obj->fileHash);
                $obj->mimetype = $file->getMimeType();
                $obj->relativePath = $fs->getLearningMaterialFilePath($file);
                $obj->filesize = $file->getSize();
            } else {
                unset($obj->mimetype);
                unset($obj->relativePath);
                unset($obj->filesize);
            }
            if ($file) {
                $fs->storeLearningMaterialFile($file, true);
            }

            return $obj;
        }, $data);

        $class = $manager->getClass();
        $entities = [];
        foreach ($dataWithFilesAttributes as $obj) {
            $relativePath = property_exists($obj, 'relativePath')?$obj->relativePath:null;
            unset($obj->relativePath);
            $json = json_encode($obj);
            $serializer = $this->getSerializer();
            /** @var LearningMaterialInterface $entity */
            $entity = $serializer->deserialize($json, $class, 'json');
            if ($relativePath) {
                $entity->setRelativePath($relativePath);
            }
            $manager->update($entity, false);
            $this->validateAndAuthorizeEntities([$entity], 'create');

            $entities[] = $entity;
        }
        $manager->flush();

        foreach ($entities as $entity) {
            $entity->generateToken();
            $manager->update($entity, false);
        }
        $manager->flush();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

    /**
     * When saving a learning material do not allow
     * the modification of file fields.  These are not
     * technically read only, but should not be writable when saved.
     */
    public function putAction($version, $object, $id, Request $request)
    {
        $manager = $this->getManager($object);
        $entity = $manager->findOneBy(['id'=> $id]);

        if ($entity) {
            $code = Response::HTTP_OK;
            $permission = 'edit';
        } else {
            $entity = $manager->create();
            $code = Response::HTTP_CREATED;
            $permission = 'create';
        }

        $data = $this->extractPutDataFromRequest($request, $object);
        unset($data->fileHash);
        unset($data->mimetype);
        unset($data->relativePath);
        unset($data->filesize);

        $json = json_encode($data);
        $serializer = $this->getSerializer();
        $serializer->deserialize($json, get_class($entity), 'json', ['object_to_populate' => $entity]);
        $this->validateAndAuthorizeEntities([$entity], $permission);

        $manager->update($entity, true, false);

        return $this->createResponse($this->getSingularResponseKey($object), $entity, $code);
    }

    /**
     * Decorate materials with a factory to add absolute links to files
     * @inheritdoc
     */
    protected function createResponse($responseKey, $value, $responseCode)
    {
        $factory = $this->learningMaterialDecoratorFactory;
        if (is_array($value)) {
            $value = array_map(function ($lm) use ($factory) {
                return $factory->create($lm);
            }, $value);
        } else {
            $value = $factory->create($value);
        }


        return parent::createResponse($responseKey, $value, $responseCode);
    }

    /**
     * Use validation groups to validate learning materials
     * since different rules are applied to them based on the
     * type of material they are.
     *
     * @inheritdoc
     * @param LearningMaterialInterface $entity
     */
    protected function validateEntity($entity)
    {
        $errors = $this->validator->validate($entity, null, $entity->getValidationGroups());
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }
}
