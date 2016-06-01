<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\JsonResponse;

use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Entity\LearningMaterialInterface;

/**
 * Class LearningMaterialController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("LearningMaterials")
 */
class LearningMaterialController extends FOSRestController
{
    /**
     * Get a LearningMaterial
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Get a LearningMaterial.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="LearningMaterial identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Classes\LearningMaterialDecorator",
     *   statusCodes={
     *     200 = "LearningMaterial.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param $id
     *
     * @return Response
     */
    public function getAction($id)
    {
        $learningMaterial = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('view', $learningMaterial)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        $factory = $this->get('ilioscore.learningmaterial_decorator.factory');

        $answer['learningMaterials'][] = $factory->create($learningMaterial);

        return $answer;
    }

    /**
     * Get all LearningMaterial.
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Get all LearningMaterial.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\LearningMaterial",
     *   statusCodes = {
     *     200 = "List of all LearningMaterial",
     *     204 = "No content. Nothing to list."
     *   }
     * )
     *
     * @QueryParam(
     *   name="offset",
     *   requirements="\d+",
     *   nullable=true,
     *   description="Offset from which to start listing notes."
     * )
     * @QueryParam(
     *   name="limit",
     *   requirements="\d+",
     *   default="20",
     *   description="How many notes to return."
     * )
     * @QueryParam(
     *   name="order_by",
     *   nullable=true,
     *   array=true,
     *   description="Order by fields. Must be an array ie. &order_by[name]=ASC&order_by[description]=DESC"
     * )
     * @QueryParam(
     *   name="filters",
     *   nullable=true,
     *   array=true,
     *   description="Filter by fields. Must be an array ie. &filters[id]=3"
     * )
     * @QueryParam(
     *   name="q",
     *   nullable=true,
     *   description="string search term to compare to title"
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param ParamFetcherInterface $paramFetcher
     *
     * @return Response
     */
    public function cgetAction(ParamFetcherInterface $paramFetcher)
    {
        $offset = $paramFetcher->get('offset');
        $limit = $paramFetcher->get('limit');
        $orderBy = $paramFetcher->get('order_by');
        $q = !is_null($paramFetcher->get('q')) ? $paramFetcher->get('q') : false;
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);
        if (array_key_exists('uploadDate', $criteria)) {
            $criteria['uploadDate'] = new \DateTime($criteria['uploadDate']);
        }

        $manager = $this->container->get('ilioscore.learningmaterial.manager');
        if ($q) {
            $result = $manager->findLearningMaterialsByQ($q, $orderBy, $limit, $offset);
        } else {
            $result = $manager->findBy($criteria, $orderBy, $limit, $offset);
        }

        $authChecker = $this->get('security.authorization_checker');
        $result = array_filter($result, function ($entity) use ($authChecker) {
            return $authChecker->isGranted('view', $entity);
        });

        $factory = $this->get('ilioscore.learningmaterial_decorator.factory');
        $result = array_map(function (LearningMaterialInterface $learningMaterial) use ($factory) {
            return $factory->create($learningMaterial);
        }, array_values($result));


        //If there are no matches return an empty array
        $answer['learningMaterials'] = $result ? $result : [];

        return $answer;
    }

    /**
     * Create a LearningMaterial.
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Create a LearningMaterial.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterial",
     *   statusCodes={
     *     201 = "Created LearningMaterial.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(statusCode=201, serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     *
     * @return Response
     */
    public function postAction(Request $request)
    {
        try {
            $postData = $this->getPostData($request);
            $file = false;
            $relativePath = false;
            if (array_key_exists('fileHash', $postData)) {
                $fileHash = $postData['fileHash'];
                $temporaryFileSystem = $this->container->get('ilioscore.temporary_filesystem');
                $file = $temporaryFileSystem->getFile($fileHash);

                if (!$file->isReadable()) {
                    return new JsonResponse(array(
                        'errors' => 'This "fileHash" is not valid'
                    ), JsonResponse::HTTP_BAD_REQUEST);
                }
                $fs = $this->container->get('ilioscore.filesystem');
                unset($postData['fileHash']);
                unset($postData['uploadDate']);
                $postData['mimetype'] = $file->getMimeType();
                $relativePath = $fs->getLearningMaterialFilePath($file);
                $postData['filesize'] = $file->getSize();
            }

            $handler = $this->container->get('ilioscore.learningmaterial.handler');
            /* @var LearningMaterialInterface $learningMaterial */
            $learningMaterial = $handler->post($postData);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('create', $learningMaterial)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            if ($file) {
                $fs->storeLearningMaterialFile($file, true);
            }

            if ($relativePath) {
                $learningMaterial->setRelativePath($relativePath);
            }

            $manager = $this->container->get('ilioscore.learningmaterial.manager');
            $manager->update($learningMaterial, true, false);

            $factory = $this->get('ilioscore.learningmaterial_decorator.factory');

            $answer['learningMaterials'] = [$factory->create($learningMaterial)];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a LearningMaterial.
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Update a LearningMaterial entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterial",
     *   statusCodes={
     *     200 = "Updated LearningMaterial.",
     *     201 = "Created LearningMaterial.",
     *     400 = "Bad Request.",
     *     404 = "Not Found."
     *   }
     * )
     *
     * @Rest\View(serializerEnableMaxDepthChecks=true)
     *
     * @param Request $request
     * @param $id
     *
     * @return Response
     */
    public function putAction(Request $request, $id)
    {
        try {
            $manager = $this->container->get('ilioscore.learningmaterial.manager');
            /** @var LearningMaterialInterface $learningMaterial */
            $learningMaterial = $manager->findOneBy(['id'=> $id]);

            if ($learningMaterial) {
                $code = Codes::HTTP_OK;
            } else {
                $learningMaterial = $manager->create();
                $code = Codes::HTTP_CREATED;
            }
            $postData = $this->getPostData($request);
            unset($postData['fileHash']);
            unset($postData['uploadDate']);


            $handler = $this->container->get('ilioscore.learningmaterial.handler');
            $learningMaterial = $handler->put($learningMaterial, $postData);

            $authChecker = $this->get('security.authorization_checker');
            if (! $authChecker->isGranted('edit', $learningMaterial)) {
                throw $this->createAccessDeniedException('Unauthorized access!');
            }

            $manager->update($learningMaterial, true, true);

            $factory = $this->get('ilioscore.learningmaterial_decorator.factory');
            $answer['learningMaterial'] = $factory->create($learningMaterial);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Delete a LearningMaterial.
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Delete a LearningMaterial entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "LearningMaterial identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted LearningMaterial.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal LearningMaterialInterface $learningMaterial
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $learningMaterial = $this->getOr404($id);

        $authChecker = $this->get('security.authorization_checker');
        if (! $authChecker->isGranted('delete', $learningMaterial)) {
            throw $this->createAccessDeniedException('Unauthorized access!');
        }

        try {
            $manager = $this->container->get('ilioscore.learningmaterial.manager');
            $manager->delete($learningMaterial);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed: " . $exception->getMessage());
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return LearningMaterialInterface $learningMaterial
     */
    protected function getOr404($id)
    {
        $manager = $this->container->get('ilioscore.learningmaterial.manager');
        $learningMaterial = $manager->findOneBy(['id' => $id]);
        if (!$learningMaterial) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $learningMaterial;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        if ($request->request->has('learningMaterial')) {
            return $request->request->get('learningMaterial');
        }

        return $request->request->all();
    }
}
