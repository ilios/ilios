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
use Ilios\CoreBundle\Handler\CurriculumInventoryAcademicLevelHandler;
use Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevelInterface;

/**
 * Class CurriculumInventoryAcademicLevelController
 * @package Ilios\CoreBundle\Controller
 * @RouteResource("CurriculumInventoryAcademicLevels")
 */
class CurriculumInventoryAcademicLevelController extends FOSRestController
{
    /**
     * Get a CurriculumInventoryAcademicLevel
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Get a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   requirements={
     *     {
     *        "name"="id",
     *        "dataType"="integer",
     *        "requirement"="\d+",
     *        "description"="CurriculumInventoryAcademicLevel identifier."
     *     }
     *   },
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "CurriculumInventoryAcademicLevel.",
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
        $answer['curriculumInventoryAcademicLevels'][] = $this->getOr404($id);

        return $answer;
    }

    /**
     * Get all CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Get all CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes = {
     *     200 = "List of all CurriculumInventoryAcademicLevel",
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

        $result = $this->getCurriculumInventoryAcademicLevelHandler()
            ->findCurriculumInventoryAcademicLevelsBy(
                $criteria,
                $orderBy,
                $limit,
                $offset
            );

        //If there are no matches return an empty array
        $answer['curriculumInventoryAcademicLevels'] =
            $result ? $result : new ArrayCollection([]);

        return $answer;
    }

    /**
     * Create a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Create a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     201 = "Created CurriculumInventoryAcademicLevel.",
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
            $new  =  $this->getCurriculumInventoryAcademicLevelHandler()
                ->post($this->getPostData($request));
            $answer['curriculumInventoryAcademicLevels'] = [$new];

            $view = $this->view($answer, Codes::HTTP_CREATED);

            return $this->handleView($view);
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }
    }

    /**
     * Update a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Update a CurriculumInventoryAcademicLevel entity.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryAcademicLevel.",
     *     201 = "Created CurriculumInventoryAcademicLevel.",
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
            $curriculumInventoryAcademicLevel = $this->getCurriculumInventoryAcademicLevelHandler()
                ->findCurriculumInventoryAcademicLevelBy(['id'=> $id]);
            if ($curriculumInventoryAcademicLevel) {
                $code = Codes::HTTP_OK;
            } else {
                $curriculumInventoryAcademicLevel = $this->getCurriculumInventoryAcademicLevelHandler()
                    ->createCurriculumInventoryAcademicLevel();
                $code = Codes::HTTP_CREATED;
            }

            $answer['curriculumInventoryAcademicLevel'] =
                $this->getCurriculumInventoryAcademicLevelHandler()->put(
                    $curriculumInventoryAcademicLevel,
                    $this->getPostData($request)
                );
        } catch (InvalidFormException $exception) {
            return $exception->getForm();
        }

        $view = $this->view($answer, $code);

        return $this->handleView($view);
    }

    /**
     * Partial Update to a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Partial Update to a CurriculumInventoryAcademicLevel.",
     *   resource = true,
     *   input="Ilios\CoreBundle\Form\Type\CurriculumInventoryAcademicLevelType",
     *   output="Ilios\CoreBundle\Entity\CurriculumInventoryAcademicLevel",
     *   requirements={
     *     {
     *         "name"="id",
     *         "dataType"="integer",
     *         "requirement"="\d+",
     *         "description"="CurriculumInventoryAcademicLevel identifier."
     *     }
     *   },
     *   statusCodes={
     *     200 = "Updated CurriculumInventoryAcademicLevel.",
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
        $answer['curriculumInventoryAcademicLevel'] =
            $this->getCurriculumInventoryAcademicLevelHandler()->patch(
                $this->getOr404($id),
                $this->getPostData($request)
            );

        return $answer;
    }

    /**
     * Delete a CurriculumInventoryAcademicLevel.
     *
     * @ApiDoc(
     *   section = "CurriculumInventoryAcademicLevel",
     *   description = "Delete a CurriculumInventoryAcademicLevel entity.",
     *   resource = true,
     *   requirements={
     *     {
     *         "name" = "id",
     *         "dataType" = "integer",
     *         "requirement" = "\d+",
     *         "description" = "CurriculumInventoryAcademicLevel identifier"
     *     }
     *   },
     *   statusCodes={
     *     204 = "No content. Successfully deleted CurriculumInventoryAcademicLevel.",
     *     400 = "Bad Request.",
     *     404 = "Not found."
     *   }
     * )
     *
     * @Rest\View(statusCode=204)
     *
     * @param $id
     * @internal CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     *
     * @return Response
     */
    public function deleteAction($id)
    {
        $curriculumInventoryAcademicLevel = $this->getOr404($id);

        try {
            $this->getCurriculumInventoryAcademicLevelHandler()
                ->deleteCurriculumInventoryAcademicLevel($curriculumInventoryAcademicLevel);

            return new Response('', Codes::HTTP_NO_CONTENT);
        } catch (\Exception $exception) {
            throw new \RuntimeException("Deletion not allowed");
        }
    }

    /**
     * Get a entity or throw a exception
     *
     * @param $id
     * @return CurriculumInventoryAcademicLevelInterface $curriculumInventoryAcademicLevel
     */
    protected function getOr404($id)
    {
        $curriculumInventoryAcademicLevel = $this->getCurriculumInventoryAcademicLevelHandler()
            ->findCurriculumInventoryAcademicLevelBy(['id' => $id]);
        if (!$curriculumInventoryAcademicLevel) {
            throw new NotFoundHttpException(sprintf('The resource \'%s\' was not found.', $id));
        }

        return $curriculumInventoryAcademicLevel;
    }

    /**
     * Parse the request for the form data
     *
     * @param Request $request
     * @return array
     */
    protected function getPostData(Request $request)
    {
        $data = $request->request->get('curriculumInventoryAcademicLevel');

        if (empty($data)) {
            $data = $request->request->all();
        }

        return $data;
    }

    /**
     * @return CurriculumInventoryAcademicLevelHandler
     */
    protected function getCurriculumInventoryAcademicLevelHandler()
    {
        return $this->container->get('ilioscore.curriculuminventoryacademiclevel.handler');
    }
}
