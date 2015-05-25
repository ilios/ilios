<?php

namespace Ilios\CoreBundle\Controller;

use FOS\RestBundle\Controller\Annotations\QueryParam;
use FOS\RestBundle\Controller\Annotations\RouteResource;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\Request\ParamFetcherInterface;
use FOS\RestBundle\Util\Codes;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use FOS\RestBundle\Controller\FOSRestController;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Ilios\CoreBundle\Exception\InvalidFormException;
use Ilios\CoreBundle\Handler\LearningMaterialHandler;
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
     *   output="Ilios\CoreBundle\Entity\LearningMaterial",
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
        $answer['learningMaterial'] = $this->getOr404($id);

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
        $criteria = !is_null($paramFetcher->get('filters')) ? $paramFetcher->get('filters') : [];
        $criteria = array_map(function ($item) {
            $item = $item == 'null' ? null : $item;
            $item = $item == 'false' ? false : $item;
            $item = $item == 'true' ? true : $item;

            return $item;
        }, $criteria);

        $result = $this->getLearningMaterialHandler()
            ->findLearningMaterialsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['learningMaterials'] =
            $result ? $result : new ArrayCollection([]);

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
            $learningmaterial = $this->getLearningMaterialHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_learningmaterials',
                    ['id' => $learningmaterial->getId()],
                    true
                )
            );

            return $response;
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
            $learningMaterial = $this->getLearningMaterialHandler()
                ->findLearningMaterialBy(['id'=> $id]);
            if ($learningMaterial) {
                $code = Codes::HTTP_OK;
            } else {
                $learningMaterial = $this->getLearningMaterialHandler()->createLearningMaterial();
                $code = Codes::HTTP_CREATED;
            }

            $answer['learningMaterial'] =
                $this->getLearningMaterialHandler()->put(
                    $learningMaterial,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a LearningMaterial.
     *
     * @ApiDoc(
     *   section = "LearningMaterial",
     *   description = "Partial Update to a LearningMaterial.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\LearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\LearningMaterial",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="LearningMaterial identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated LearningMaterial.",
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
    public function patchAction(Request $request, $id)
    {
        $answer['learningMaterial'] =
            $this->getLearningMaterialHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
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

        try {
            $this->getLearningMaterialHandler()->deleteLearningMaterial($learningMaterial);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
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
        $learningMaterial = $this->getLearningMaterialHandler()
            ->findLearningMaterialBy(['id' => $id]);
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
        $data = $request->request->get('learningMaterial');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return LearningMaterialHandler
     */
    protected function getLearningMaterialHandler()
    {
        return $this->container->get('ilioscore.learningmaterial.handler');
    }
}
