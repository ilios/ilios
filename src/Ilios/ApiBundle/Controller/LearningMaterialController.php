<?php

namespace Ilios\ApiBundle\Controller;

use Ilios\CoreBundle\Entity\LearningMaterialInterface;
use Ilios\CoreBundle\Entity\Manager\LearningMaterialManager;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;

/**
 * Class LearningMaterialController
 * We have to handle a special 'q' parameter on learningMaterils
 * and we need to work with a factory to produce the results
 * so it needs its own controller
 * @package Ilios\ApiBundle\Controller
 */
class LearningMaterialController extends NonDtoApiController
{
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

    public function postAction($version, $object, Request $request)
    {
        $manager = $this->getManager($object);
        $class = $manager->getClass() . '[]';

        $data = $this->extractDataFromRequest($request, $object, $singleItem = false, $returnArray = true);

        $temporaryFileSystem = $this->container->get('ilioscore.temporary_filesystem');
        $fs = $this->container->get('ilioscore.filesystem');
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

        $json = json_encode($dataWithFilesAttributes);

        $serializer = $this->getSerializer();
        $entities = $serializer->deserialize($json, $class, 'json');
        $this->validateAndAuthorizeEntities($entities, 'create');

        foreach ($entities as $entity) {
            $manager->update($entity, false);
        }
        $manager->flush();

        /** @var LearningMaterialInterface $entity */
        foreach ($entities as $entity) {
            $entity->generateToken();
            $manager->update($entity, false);
        }
        $manager->flushAndClear();

        return $this->createResponse($this->getPluralResponseKey($object), $entities, Response::HTTP_CREATED);
    }

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

        $data = $this->extractDataFromRequest($request, $object, $singleItem = true, $returnArray = true);
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

    protected function createResponse($responseKey, $value, $responseCode)
    {
        $factory = $this->get('ilioscore.learningmaterial_decorator.factory');
        if (is_array($value)) {
            $value = array_map(function (LearningMaterialInterface $lm) use ($factory) {
                return $factory->create($lm);
            }, $value);
        } else {
            $value = $factory->create($value);
        }


        return parent::createResponse($responseKey, $value, $responseCode);
    }

    /**
     * @inheritdoc
     * @param LearningMaterialInterface $entity
     */
    protected function validateEntity($entity)
    {
        $validator = $this->container->get('validator');
        $errors = $validator->validate($entity, null, $entity->getValidationGroups());
        if (count($errors) > 0) {
            $errorsString = (string) $errors;

            throw new HttpException(Response::HTTP_BAD_REQUEST, $errorsString);
        }
    }
}
