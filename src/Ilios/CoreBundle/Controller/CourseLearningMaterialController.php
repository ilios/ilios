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
use Ilios\CoreBundle\Handler\CourseLearningMaterialHandler;
use Ilios\CoreBundle\Entity\CourseLearningMaterialInterface;

/**
 * Class CourseLearningMaterialController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CourseLearningMaterials")
 */
class CourseLearningMaterialController extends FOSRestController
{
    /**
     * Get a CourseLearningMaterial
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Get a CourseLearningMaterial.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CourseLearningMaterial identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CourseLearningMaterial",
     *   statusCodes={
     *     200 = "CourseLearningMaterial.",
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
        $answer['courseLearningMaterial'] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CourseLearningMaterial.
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Get all CourseLearningMaterial.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CourseLearningMaterial",
     *   statusCodes = {
     *     200 = "List of all CourseLearningMaterial",
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

        $result = $this->getCourseLearningMaterialHandler()
            ->findCourseLearningMaterialsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['courseLearningMaterials'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CourseLearningMaterial.
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Create a CourseLearningMaterial.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseLearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\CourseLearningMaterial",
     *   statusCodes={
     *     201 = "Created CourseLearningMaterial.",
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
            $courselearningmaterial = $this->getCourseLearningMaterialHandler()
                ->post($this->getPostData($request));

            $response = new Response();
            $response->setStatusCode(Codes::HTTP_CREATED);
            $response->headers->set(
                'Location',
                $this->generateUrl(
                    'get_courselearningmaterials',
                    ['id' => $courselearningmaterial->getId()],
                    true
                )
            );

            return $response;
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CourseLearningMaterial.
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Update a CourseLearningMaterial entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseLearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\CourseLearningMaterial",
     *   statusCodes={
     *     200 = "Updated CourseLearningMaterial.",
     *     201 = "Created CourseLearningMaterial.",
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
            $courseLearningMaterial = $this->getCourseLearningMaterialHandler()
                ->findCourseLearningMaterialBy(['id'=> $id]);
            if ($courseLearningMaterial) {
                $code = Codes::HTTP_OK;
            } else {
                $courseLearningMaterial = $this->getCourseLearningMaterialHandler()->createCourseLearningMaterial();
                $code = Codes::HTTP_CREATED;
            }

            $answer['courseLearningMaterial'] =
                $this->getCourseLearningMaterialHandler()->put(
                    $courseLearningMaterial,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CourseLearningMaterial.
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Partial Update to a CourseLearningMaterial.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CourseLearningMaterialType",
     *   output="Ilios\CoreBundle\Entity\CourseLearningMaterial",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="CourseLearningMaterial identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated CourseLearningMaterial.",
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
        $answer['courseLearningMaterial'] =
            $this->getCourseLearningMaterialHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a CourseLearningMaterial.
     *
     * @ApiDoc(
     *   section = "CourseLearningMaterial",
     *   description = "Delete a CourseLearningMaterial entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CourseLearningMaterial identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CourseLearningMaterial.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CourseLearningMaterialInterface $courseLearningMaterial
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $courseLearningMaterial = $this->getOr404($id);

        try {
            $this->getCourseLearningMaterialHandler()->deleteCourseLearningMaterial($courseLearningMaterial);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CourseLearningMaterialInterface $courseLearningMaterial
     */
    protected function getOr404($id)
    {
        $courseLearningMaterial = $this->getCourseLearningMaterialHandler()
            ->findCourseLearningMaterialBy(['id' => $id]);
        if (!$courseLearningMaterial) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $courseLearningMaterial;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('courseLearningMaterial');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CourseLearningMaterialHandler
     */
    protected function getCourseLearningMaterialHandler()
    {
        return $this->container->get('ilioscore.courselearningmaterial.handler');
    }
}
